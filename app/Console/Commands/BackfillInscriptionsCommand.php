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
                            {--batch=100 : DB insert batch size}
                            {--fill-gaps : Only fetch missing inscription numbers}';

    protected $description = 'Backfill inscriptions from ord-pepecoin into the local database';

    private bool $shouldStop = false;

    public function handle(): int
    {
        $this->trap([SIGINT, SIGTERM], function () {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('Received shutdown signal. Finishing current batch...');
        });

        if ($this->option('fill-gaps')) {
            return $this->fillGaps();
        }

        return $this->backfill();
    }

    private function backfill(): int
    {
        $url = rtrim(config('pepecoin.ordinals.url'), '/');
        $timeout = config('pepecoin.ordinals.timeout', 10);
        $concurrency = (int) $this->option('concurrency');
        $batchSize = (int) $this->option('batch');
        $limit = (int) $this->option('limit');

        $current = $this->option('from') !== null
            ? (int) $this->option('from')
            : (int) (Inscription::max('id') ?? -1) + 1;

        $imported = 0;
        $rows = [];
        $failedNumbers = [];

        $this->info("Starting backfill from inscription #{$current} (concurrency: {$concurrency})");

        while (! $this->shouldStop && ($limit === 0 || $imported < $limit)) {
            $chunkSize = $limit > 0
                ? min($concurrency, $limit - $imported)
                : $concurrency;

            $numbers = range($current, $current + $chunkSize - 1);

            [$successful, $failed] = $this->fetchBatch($numbers, $url, $timeout, $concurrency);

            $rows = array_merge($rows, $successful);
            $failedNumbers = array_merge($failedNumbers, $failed);
            $imported += count($successful);

            if (count($rows) >= $batchSize) {
                DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
                $rows = [];
            }

            $current += $chunkSize;

            if (count($failed) === $chunkSize) {
                $this->warn("All {$chunkSize} requests in batch failed at #{$current}. Stopping.");
                break;
            }

            if ($imported % 1000 < $concurrency) {
                $this->output->write("\r  Imported: {$imported} | Current: #{$current} | Failed: ".count($failedNumbers));
            }
        }

        if (! empty($rows)) {
            DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
        }

        if (! empty($failedNumbers) && ! $this->shouldStop) {
            $this->newLine();
            $this->retryFailed($failedNumbers, $url, $timeout);
        }

        $this->newLine();
        $this->info("Done. Imported {$imported} inscriptions.");

        return self::SUCCESS;
    }

    private function fillGaps(): int
    {
        $url = rtrim(config('pepecoin.ordinals.url'), '/');
        $timeout = config('pepecoin.ordinals.timeout', 10);
        $concurrency = (int) $this->option('concurrency');
        $batchSize = (int) $this->option('batch');

        $maxId = (int) Inscription::max('id');

        if ($maxId === 0) {
            $this->info('No inscriptions in database.');

            return self::SUCCESS;
        }

        $this->info("Scanning for gaps in inscriptions 0..{$maxId}");

        $existing = Inscription::orderBy('id')->pluck('id')->flip();
        $missing = [];

        for ($i = 0; $i <= $maxId; $i++) {
            if (! $existing->has($i)) {
                $missing[] = $i;
            }
        }

        if (empty($missing)) {
            $this->info('No gaps found.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($missing).' missing inscriptions. Fetching...');

        $imported = 0;
        $rows = [];
        $failedNumbers = [];

        foreach (array_chunk($missing, $concurrency) as $chunk) {
            if ($this->shouldStop) {
                break;
            }

            [$successful, $failed] = $this->fetchBatch($chunk, $url, $timeout, $concurrency);

            $rows = array_merge($rows, $successful);
            $failedNumbers = array_merge($failedNumbers, $failed);
            $imported += count($successful);

            if (count($rows) >= $batchSize) {
                DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
                $rows = [];
            }

            $this->output->write("\r  Filled: {$imported}/".count($missing).' | Failed: '.count($failedNumbers));
        }

        if (! empty($rows)) {
            DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
        }

        if (! empty($failedNumbers) && ! $this->shouldStop) {
            $this->newLine();
            $this->retryFailed($failedNumbers, $url, $timeout);
        }

        $this->newLine();
        $this->info("Done. Filled {$imported} gaps.");

        return self::SUCCESS;
    }

    /**
     * @param  int[]  $numbers
     * @return array{0: array<array<string, mixed>>, 1: int[]}
     */
    private function fetchBatch(array $numbers, string $url, int $timeout, int $concurrency): array
    {
        $responses = Http::pool(fn (Pool $pool) => collect($numbers)->map(
            fn (int $n) => $pool->as((string) $n)
                ->acceptJson()
                ->timeout($timeout)
                ->get("{$url}/inscription/{$n}")
        )->all(), concurrency: $concurrency);

        $successful = [];
        $failed = [];

        foreach ($numbers as $number) {
            $response = $responses[(string) $number];

            if ($response instanceof ConnectionException || ! $response->ok()) {
                $failed[] = $number;

                continue;
            }

            try {
                $data = InscriptionData::from($response->json());
                $successful[] = FetchBlockInscriptions::mapToRow($data);
            } catch (\Throwable) {
                $failed[] = $number;
            }
        }

        return [$successful, $failed];
    }

    /**
     * @param  int[]  $failedNumbers
     */
    private function retryFailed(array $failedNumbers, string $url, int $timeout): void
    {
        $this->info('Retrying '.count($failedNumbers).' failed inscriptions individually...');

        $retried = 0;
        $rows = [];

        foreach ($failedNumbers as $number) {
            if ($this->shouldStop) {
                break;
            }

            try {
                $response = Http::acceptJson()
                    ->timeout($timeout)
                    ->retry(3, 1000)
                    ->get("{$url}/inscription/{$number}");

                if (! $response->ok()) {
                    continue;
                }

                $data = InscriptionData::from($response->json());
                $rows[] = FetchBlockInscriptions::mapToRow($data);
                $retried++;
            } catch (\Throwable) {
                continue;
            }
        }

        if (! empty($rows)) {
            DB::table('inscriptions')->upsert($rows, ['id'], array_keys($rows[0]));
        }

        $this->info("Retried: {$retried}/".count($failedNumbers).' recovered.');
    }
}
