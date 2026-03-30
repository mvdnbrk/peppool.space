<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Data\Ordinals\InscriptionData;
use App\Models\Inscription;
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

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public readonly int $height,
    ) {}

    public function handle(OrdinalsService $ordinals): void
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
                $rows[] = self::mapToRow($data);
            } catch (RequestException) {
                continue;
            }
        }

        if (! empty($rows)) {
            DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
        }
    }

    /**
     * @param  InscriptionData  $data
     * @return array<string, mixed>
     */
    public static function mapToRow($data): array
    {
        $contentType = $data->content_type
            ? strtolower(trim(explode(';', $data->content_type)[0]))
            : null;

        $contentEncoding = null;
        if ($data->content_type && str_contains($data->content_type, 'charset=')) {
            $contentEncoding = trim(explode('charset=', $data->content_type)[1] ?? '');
        }

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
}
