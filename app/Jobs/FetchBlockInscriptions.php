<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Data\Ordinals\InscriptionData;
use App\Models\Inscription;
use App\Services\Inscriptions\InscriptionClassificationParser;
use App\Services\OrdinalsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class FetchBlockInscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private const MAX_CONTENT_LENGTH = 512;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public readonly int $height,
    ) {}

    public function handle(OrdinalsService $ordinals, InscriptionClassificationParser $parser): void
    {
        try {
            $inscriptionIds = $ordinals->getBlockInscriptionIds($this->height);
        } catch (RequestException $e) {
            if ($e->response->status() === 404) {
                return;
            }

            throw $e;
        }

        if (empty($inscriptionIds)) {
            return;
        }

        $existing = Inscription::whereIn('inscription_id', $inscriptionIds)
            ->pluck('inscription_id')
            ->flip();

        $rows = [];

        foreach ($inscriptionIds as $inscriptionId) {
            if ($existing->has($inscriptionId)) {
                continue;
            }

            try {
                $data = $ordinals->getInscription($inscriptionId);
                $row = self::mapToRow($data);

                if (self::shouldFetchContent($data)) {
                    $content = $ordinals->getContent($inscriptionId)->body();
                    $parsed = $parser->parse($data->content_type, $content);
                    if (mb_check_encoding($content, 'UTF-8')) {
                        $row['content'] = $content;
                    } else {
                        $row['flags'] |= Inscription::FLAG_GARBAGE;
                    }
                    $row['flags'] |= $parsed['flags'];
                }

                $rows[] = $row;
            } catch (RequestException) {
                continue;
            }
        }

        if (! empty($rows)) {
            DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
        }
    }

    public static function shouldFetchContent(InscriptionData $data): bool
    {
        if ($data->content_length === null || $data->content_length > self::MAX_CONTENT_LENGTH) {
            return false;
        }

        if ($data->content_type === null) {
            return false;
        }

        $type = strtolower(explode(';', $data->content_type)[0]);

        return str_starts_with($type, 'text/') || $type === 'application/json';
    }

    /**
     * @param  InscriptionData  $data
     * @return array<string, mixed>
     */
    public static function mapToRow($data): array
    {
        $flags = 0;

        if ($data->parent_count > 1) {
            $flags |= Inscription::FLAG_MULTI_PARENT;
        }

        if ($data->delegate) {
            $flags |= Inscription::FLAG_DELEGATE;
        }

        if (! empty($data->properties['title'])) {
            $flags |= Inscription::FLAG_HAS_TITLE;
        }

        if (! empty($data->properties['traits']) && is_array($data->properties['traits'])) {
            $flags |= Inscription::FLAG_HAS_TRAITS;
        }

        $contentType = self::sanitizeUtf8($data->content_type);
        $contentEncoding = self::sanitizeUtf8($data->content_encoding ?? null);

        if ($contentType !== $data->content_type || $contentEncoding !== ($data->content_encoding ?? null)) {
            $flags |= Inscription::FLAG_GARBAGE;
        }

        return [
            'id' => $data->number,
            'inscription_id' => $data->id,
            'parent_id' => $data->parents[0] ?? null,
            'delegate_id' => $data->delegate,
            'content_encoding' => $contentEncoding,
            'content_type' => $contentType,
            'content_length' => $data->content_length,
            'content' => null,
            'properties' => ! empty($data->properties) ? json_encode($data->properties) : null,
            'flags' => $flags,
            'block' => $data->height,
        ];
    }

    private static function sanitizeUtf8(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_check_encoding($value, 'UTF-8') ? $value : null;
    }
}
