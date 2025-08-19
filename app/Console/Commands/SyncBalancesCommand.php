<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncBalancesCommand extends Command
{
    protected $signature = 'pepe:sync:balances
                            {--address= : Recalculate balance for a specific address}
                            {--batch=1000 : Number of addresses to process in each batch}';

    protected $description = 'Recalculate balances for all addresses or a specific address';

    protected int $processedAddresses = 0;

    protected bool $shouldStop = false;

    public function handle(): int
    {
        // Set up graceful shutdown handling
        $this->trap([SIGINT, SIGTERM], function (int $signal) {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('🛑 Received shutdown signal. Finishing current batch and stopping gracefully...');
            $this->line('💡 Press Ctrl+C again to force quit (may cause data corruption).');
        });

        $specificAddress = $this->option('address');
        $batchSize = (int) $this->option('batch');

        $this->info('Starting balance recalculation'.($specificAddress ? " for address: {$specificAddress}" : ' for all addresses'));

        try {
            if ($specificAddress) {
                $this->recalculateAddressBalance($specificAddress);
                $this->processedAddresses++;
            } else {
                $this->recalculateAllAddresses($batchSize);
            }

            $this->displaySummary();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
            Log::error('Balance sync error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    private function recalculateAllAddresses(int $batchSize): void
    {
        $query = DB::table('transaction_outputs')
            ->select('address')
            ->distinct()
            ->whereNotNull('address')
            ->orderBy('address');

        $totalAddresses = (clone $query)->count();
        $this->line("Found {$totalAddresses} addresses to process");

        if ($totalAddresses === 0) {
            $this->info('No addresses need balance recalculation.');

            return;
        }

        $progressBar = $this->output->createProgressBar($totalAddresses);
        $progressBar->start();

        $shouldContinue = true;
        $query->chunk($batchSize, function ($addresses) use ($progressBar, &$shouldContinue) {
            if (! $shouldContinue) {
                return false; // Stop further chunking
            }

            foreach ($addresses as $addressData) {
                if ($this->shouldStop) {
                    $shouldContinue = false;

                    return false; // Stop chunking
                }

                $this->recalculateAddressBalance($addressData->address);
                $this->processedAddresses++;
                $progressBar->advance();

                // Small delay to prevent database overload
                usleep(1000); // 1ms
            }
        });

        $progressBar->finish();
        $this->newLine(2);
    }

    private function recalculateAddressBalance(string $address): void
    {
        DB::transaction(function () use ($address) {
            // Calculate balance from unspent outputs
            $balance = DB::table('transaction_outputs')
                ->where('address', $address)
                ->where('is_spent', false)
                ->sum('amount');

            // Calculate total received
            $totalReceived = DB::table('transaction_outputs')
                ->where('address', $address)
                ->sum('amount');

            // Calculate total sent (from inputs)
            $totalSent = DB::table('transaction_inputs')
                ->where('address', $address)
                ->sum('amount');

            // Get transaction count
            $txCount = DB::table('transaction_outputs')
                ->where('address', $address)
                ->distinct('tx_id')
                ->count('tx_id');

            // Get first and last activity
            $firstSeen = DB::table('transaction_outputs as to')
                ->join('transactions as t', 'to.tx_id', '=', 't.tx_id')
                ->join('blocks as b', 't.block_height', '=', 'b.height')
                ->where('to.address', $address)
                ->min('b.created_at');

            $lastActivity = DB::table('transaction_outputs as to')
                ->join('transactions as t', 'to.tx_id', '=', 't.tx_id')
                ->join('blocks as b', 't.block_height', '=', 'b.height')
                ->where('to.address', $address)
                ->max('b.created_at');

            // Update or create address balance record
            DB::table('address_balances')->updateOrInsert(
                ['address' => $address],
                [
                    'balance' => $balance,
                    'total_received' => $totalReceived,
                    'total_sent' => $totalSent,
                    'tx_count' => $txCount,
                    'first_seen' => $firstSeen,
                    'last_activity' => $lastActivity,
                ]
            );
        });
    }

    private function displaySummary(): void
    {
        $this->info('✅ Balance sync completed successfully!');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Addresses Processed', number_format($this->processedAddresses)],
            ]
        );

        // Show database statistics
        $totalAddresses = DB::table('address_balances')->count();
        $totalBalance = DB::table('address_balances')->sum('balance');
        $totalReceived = DB::table('address_balances')->sum('total_received');
        $totalSent = DB::table('address_balances')->sum('total_sent');

        $this->info('📊 Database Statistics:');
        $this->table(
            ['Statistic', 'Value'],
            [
                ['Total Addresses', number_format($totalAddresses)],
                ['Total Balance', number_format($totalBalance, 8).' PEPE'],
                ['Total Received', number_format($totalReceived, 8).' PEPE'],
                ['Total Sent', number_format($totalSent, 8).' PEPE'],
            ]
        );
    }
}
