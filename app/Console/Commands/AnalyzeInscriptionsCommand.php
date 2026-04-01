<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Inscription;
use App\Services\InscriptionContentParser;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AnalyzeInscriptionsCommand extends Command
{
    protected $signature = 'pepe:analyze:inscriptions
                            {--batch=500 : Number of inscriptions to process per batch}
                            {--concurrency=20 : Number of concurrent content fetches}
                            {--max-length=512 : Maximum content_length to fetch}
                            {--dry-run : Show what would be processed without making changes}';

    protected $description = 'Analyze unprocessed inscriptions: fetch content, classify (pepemap/prc-20), and update flags';

    private bool $shouldStop = false;

    public function handle(InscriptionContentParser $parser): int
    {
        $this->trap([SIGINT, SIGTERM], function () {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('Received shutdown signal. Finishing current batch...');
        });

        $url = rtrim(config('pepecoin.ordinals.url'), '/');
        $timeout = config('pepecoin.ordinals.timeout', 10);
        $batchSize = (int) $this->option('batch');
        $concurrency = (int) $this->option('concurrency');
        $maxLength = (int) $this->option('max-length');
        $dryRun = (bool) $this->option('dry-run');

        $query = Inscription::query()
            ->whereRaw('flags & ? = 0', [Inscription::FLAG_ANALYZED])
            ->where('content_length', '<=', $maxLength)
            ->where(function ($q) {
                $q->where('content_type', 'like', 'text/%')
                    ->orWhere('content_type', 'like', 'application/json%');
            })
            ->orderBy('id');

        $total = $query->count();

        if ($total === 0) {
            $this->info('No unanalyzed inscriptions found.');

            return self::SUCCESS;
        }

        $this->info("Found {$total} inscriptions to analyze".($dryRun ? ' (dry run)' : ''));

        if ($dryRun) {
            return self::SUCCESS;
        }

        $processed = 0;
        $stats = ['pepemap' => 0, 'prc20_valid' => 0, 'prc20_invalid' => 0, 'other' => 0, 'garbage' => 0, 'failed' => 0];

        $query->chunk($batchSize, function ($inscriptions) use (
            $parser, $url, $timeout, $concurrency,
            &$processed, &$stats, $total,
        ) {
            if ($this->shouldStop) {
                return false;
            }

            foreach ($inscriptions->chunk($concurrency) as $chunk) {
                if ($this->shouldStop) {
                    return false;
                }

                $responses = Http::pool(fn (Pool $pool) => $chunk->map(
                    fn (Inscription $inscription) => $pool->as($inscription->inscription_id)
                        ->timeout($timeout)
                        ->get("{$url}/content/{$inscription->inscription_id}")
                )->all(), concurrency: $concurrency);

                $updates = [];

                foreach ($chunk as $inscription) {
                    $response = $responses[$inscription->inscription_id] ?? null;

                    if ($response === null || $response instanceof \Throwable || ! $response->ok()) {
                        $stats['failed']++;

                        continue;
                    }

                    $content = $response->body();
                    $parsed = $parser->parse($inscription->content_type, $content);

                    if ($parsed['flags'] & Inscription::FLAG_BITMAP) {
                        $stats['pepemap']++;
                    } elseif ($parsed['prc20'] !== null) {
                        $parsed['prc20']['valid'] ? $stats['prc20_valid']++ : $stats['prc20_invalid']++;
                    } else {
                        $stats['other']++;
                    }

                    $isGarbage = ! mb_check_encoding($content, 'UTF-8');
                    $flags = $inscription->flags | $parsed['flags'];

                    if ($isGarbage) {
                        $flags |= Inscription::FLAG_GARBAGE;
                        $stats['garbage']++;
                    }

                    $updates[] = [
                        'id' => $inscription->id,
                        'content' => $isGarbage ? null : $content,
                        'flags' => $flags,
                    ];
                }

                if (! empty($updates)) {
                    foreach ($updates as $update) {
                        DB::table('inscriptions')
                            ->where('id', $update['id'])
                            ->update(['content' => $update['content'], 'flags' => $update['flags']]);
                    }
                }

                $processed += $chunk->count();
                $this->output->write("\r  Progress: {$processed}/{$total} | Pepemap: {$stats['pepemap']} | PRC-20 valid: {$stats['prc20_valid']} | PRC-20 invalid: {$stats['prc20_invalid']} | Other: {$stats['other']} | Garbage: {$stats['garbage']} | Failed: {$stats['failed']}");
            }
        });

        $this->newLine(2);
        $this->table(
            ['Type', 'Count'],
            collect($stats)->map(fn ($count, $type) => [str_replace('_', ' ', $type), $count])->values()
        );
        $this->info("Done. Processed {$processed} inscriptions.");

        return self::SUCCESS;
    }
}
