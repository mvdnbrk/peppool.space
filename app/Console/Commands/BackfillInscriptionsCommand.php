<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Data\Ordinals\InscriptionData;
use App\Jobs\FetchBlockInscriptions;
use App\Models\Inscription;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BackfillInscriptionsCommand extends Command
{
    protected $signature = 'pepe:backfill:inscriptions
                            {--from= : Start from this inscription number (defaults to latest in DB + 1)}
                            {--limit=0 : Maximum number of inscriptions to process (0 = unlimited)}
                            {--concurrency=20 : Number of concurrent HTTP requests}
                            {--batch=100 : DB insert batch size}';

    protected $description = 'Backfill inscriptions from ord-pepecoin into the local database';

    private bool $shouldStop = false;

    public function handle(): int
    {
        $this->trap([SIGINT, SIGTERM], function () {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('Received shutdown signal. Finishing current batch...');
        });

        $url = rtrim(config('pepecoin.ordinals.url'), '/');
        $timeout = config('pepecoin.ordinals.timeout', 10);
        $concurrency = (int) $this->option('concurrency');
        $batchSize = (int) $this->option('batch');
        $limit = (int) $this->option('limit');

        $current = $this->option('from') !== null
            ? (int) $this->option('from')
            : (int) (Inscription::max('id') ?? -1) + 1;

        $imported = 0;
        $failed = 0;
        $rows = [];

        $this->info("Starting backfill from inscription #{$current} (concurrency: {$concurrency})");

        while (! $this->shouldStop && ($limit === 0 || $imported + $failed < $limit)) {
            $chunkSize = $limit > 0
                ? min($concurrency, $limit - $imported - $failed)
                : $concurrency;

            $numbers = range($current, $current + $chunkSize - 1);

            $responses = Http::pool(fn (Pool $pool) => collect($numbers)->map(
                fn (int $n) => $pool->as((string) $n)
                    ->acceptJson()
                    ->timeout($timeout)
                    ->get("{$url}/inscription/{$n}")
            )->all(), concurrency: $concurrency);

            $batchFailed = 0;

            foreach ($numbers as $number) {
                $response = $responses[(string) $number];

                if ($response instanceof ConnectionException || ! $response->ok()) {
                    $batchFailed++;

                    continue;
                }

                try {
                    $data = InscriptionData::from($response->json());
                    $rows[] = FetchBlockInscriptions::mapToRow($data);
                    $imported++;
                } catch (\Throwable) {
                    $batchFailed++;
                }
            }

            $failed += $batchFailed;

            if (count($rows) >= $batchSize) {
                DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
                $rows = [];
            }

            $current += $chunkSize;

            if ($batchFailed === $chunkSize) {
                $this->warn("All {$chunkSize} requests in batch failed at #{$current}. Stopping.");
                break;
            }

            if ($imported % 1000 < $concurrency) {
                $this->output->write("\r  Imported: {$imported} | Current: #{$current} | Failed: {$failed}");
            }
        }

        if (! empty($rows)) {
            DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
        }

        $this->newLine();
        $this->info("Done. Imported {$imported} inscriptions, {$failed} failed.");

        return self::SUCCESS;
    }
}
