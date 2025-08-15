<?php

namespace App\Console\Commands;

use App\Services\PepecoinRpcService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncTransactionsCommand extends Command
{
    protected $signature = 'pepe:sync:transactions
                            {--from= : Start from specific block height}
                            {--to= : End at specific block height}
                            {--limit= : Limit the total number of blocks to process}
                            {--batch=250 : Number of blocks to process in each batch}
                            {--delay=5 : Delay in milliseconds between RPC calls}
                            {--batch-delay=500 : Delay in milliseconds between batches}
                            {--force : Force re-sync even if transactions already exist}';

    protected $description = 'Sync transactions, inputs, outputs and address balances from blockchain';

    private PepecoinRpcService $rpc;

    private bool $shouldStop = false;

    private array $affectedAddresses = [];

    private int $processedBlocks = 0;

    private int $processedTransactions = 0;

    private int $processedAddresses = 0;

    private $progressBar = null;

    private int $addressBatchSize = 250;

    public function handle(PepecoinRpcService $rpc): int
    {
        $this->rpc = $rpc;
        // Set up graceful shutdown handling
        $this->trap([SIGINT, SIGTERM], function (int $signal) {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('🛑 Received shutdown signal. Finishing current batch and stopping gracefully...');
            $this->line('💡 Press Ctrl+C again to force quit (may cause data corruption).');
        });

        $this->info('🚀 Starting transaction sync for the best Pepecoin explorer!');

        // Test RPC connection
        if (! $this->rpc->testConnection()) {
            $this->error('❌ Cannot connect to Pepecoin RPC. Please check your configuration.');

            return Command::FAILURE;
        }

        $this->info('✅ RPC connection established');

        // Determine block range
        [$fromHeight, $toHeight] = $this->determineBlockRange();

        if ($fromHeight > $toHeight) {
            $this->warn('No blocks to process.');

            return Command::SUCCESS;
        }

        // Validate that all blocks in range exist in database
        $this->info('🔍 Validating block range exists in database...');
        $validationResult = $this->validateBlockRange($fromHeight, $toHeight);

        if (! $validationResult['valid']) {
            $this->error('❌ Block validation failed!');
            $this->error("Missing blocks in database: {$validationResult['missing_count']} blocks");
            $this->error('Please run pepe:sync:blocks first to sync the required blocks.');

            if (! empty($validationResult['missing_ranges'])) {
                $this->line('Missing block ranges:');
                foreach ($validationResult['missing_ranges'] as $range) {
                    $this->line("  • {$range['start']} - {$range['end']} ({$range['count']} blocks)");
                }
            }

            return Command::FAILURE;
        }

        $this->info('✅ All blocks in range exist in database');

        $totalBlocks = $toHeight - $fromHeight + 1;
        $this->info("📊 Processing {$totalBlocks} blocks {$fromHeight} to {$toHeight}");

        $this->progressBar = $this->output->createProgressBar($totalBlocks);
        $this->progressBar->setFormat('verbose');

        // Validate and parse options
        $validationResult = $this->validateOptions();
        if ($validationResult !== true) {
            return $validationResult;
        }

        $batchSize = (int) $this->option('batch');
        $delay = (int) $this->option('delay');
        $batchDelay = (int) $this->option('batch-delay');

        $this->newLine();
        $this->comment('Press Ctrl+C to stop gracefully (will finish current batch)');
        $this->newLine();

        // Process blocks in batches
        for ($height = $fromHeight; $height <= $toHeight; $height += $batchSize) {
            // Check for graceful shutdown signal
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            // Check for shutdown signal
            if ($this->shouldStop) {
                $this->warn('🛑 Graceful shutdown requested. Stopping after current batch.');
                break;
            }

            $batchEnd = min($height + $batchSize - 1, $toHeight);

            $this->processBatch($height, $batchEnd, $delay);

            // Progress bar is now advanced per block within processBatch

            // Batch delay to prevent overwhelming the RPC
            if ($batchDelay > 0 && $batchEnd < $toHeight) {
                usleep($batchDelay * 1000);
            }
        }

        // Only finish progress bar if we completed the full range
        if (! $this->shouldStop) {
            $this->progressBar->finish();
        }
        $this->newLine(2);

        $this->displaySummary();

        return Command::SUCCESS;
    }

    private function validateOptions(): bool|int
    {
        $options = [
            'from' => ['name' => '--from', 'min' => 0],
            'to' => ['name' => '--to', 'min' => 0],
            'limit' => ['name' => '--limit', 'min' => 1],
            'batch' => ['name' => '--batch', 'min' => 1],
            'delay' => ['name' => '--delay', 'min' => 0],
            'batch-delay' => ['name' => '--batch-delay', 'min' => 0],
        ];

        foreach ($options as $key => $config) {
            $value = $this->option($key);
            if ($value !== null && $value !== '' && (!is_numeric($value) || (int)$value < $config['min'])) {
                $this->error("Invalid {$config['name']} value. Must be a " . ($config['min'] > 0 ? 'positive' : 'non-negative') . " integer.");
                return Command::FAILURE;
            }
        }

        if ($this->option('from') !== null && $this->option('to') !== null && (int)$this->option('from') > (int)$this->option('to')) {
            $this->error('Invalid range: --from cannot be greater than --to.');
            return Command::FAILURE;
        }

        return true;
    }

    private function determineBlockRange(): array
    {
        $from = $this->option('from');
        $to = $this->option('to');
        $force = $this->option('force');

        // If specific range is provided, use it
        if ($from !== null && $to !== null) {
            return [(int) $from, (int) $to];
        }

        // Get the range of blocks that exist in the database
        $minBlockHeight = DB::table('blocks')->min('height') ?? 1;
        $maxBlockHeight = DB::table('blocks')->max('height') ?? 1;

        if ($minBlockHeight === null || $maxBlockHeight === null) {
            $this->warn('No blocks found in database. Please run pepe:sync:blocks first.');

            return [1, 0]; // Invalid range to trigger "no blocks to process"
        }

        // Determine starting height
        if ($from !== null) {
            $fromHeight = max((int) $from, $minBlockHeight);
        } elseif (! $force) {
            // Start from the last synced transaction block + 1, but don't go beyond available blocks
            $lastSyncedHeight = DB::table('transactions')->max('block_height') ?? ($minBlockHeight - 1);
            $fromHeight = max($lastSyncedHeight + 1, $minBlockHeight);
        } else {
            $fromHeight = $minBlockHeight; // Start from first available block if forcing
        }

        // Determine ending height - limit to blocks that exist in database
        if ($to !== null) {
            $toHeight = min((int) $to, $maxBlockHeight);
        } else {
            $toHeight = $maxBlockHeight; // Only sync up to the highest block in database
        }

        // Apply limit option if specified (limits total blocks to process)
        $limit = $this->option('limit');
        if ($limit !== null && $limit > 0) {
            $toHeight = min($toHeight, $fromHeight + (int) $limit - 1);
        }

        return [$fromHeight, $toHeight];
    }

    private function validateBlockRange(int $fromHeight, int $toHeight): array
    {
        $totalBlocks = $toHeight - $fromHeight + 1;

        // For large ranges (>10k blocks), use efficient count-based validation
        if ($totalBlocks > 10000) {
            $existingCount = DB::table('blocks')
                ->whereBetween('height', [$fromHeight, $toHeight])
                ->count();

            if ($existingCount === $totalBlocks) {
                return ['valid' => true];
            }

            // For large ranges, just return basic info without detailed missing ranges
            return [
                'valid' => false,
                'missing_count' => $totalBlocks - $existingCount,
                'missing_ranges' => [], // Skip detailed analysis for performance
                'existing_count' => $existingCount,
                'total_blocks' => $totalBlocks,
            ];
        }

        // For smaller ranges, use detailed validation (original logic)
        $existingHeights = DB::table('blocks')
            ->whereBetween('height', [$fromHeight, $toHeight])
            ->pluck('height')
            ->sort()
            ->values()
            ->toArray();

        $existingCount = count($existingHeights);

        if ($existingCount === $totalBlocks) {
            return ['valid' => true];
        }

        // Find missing blocks for small ranges only
        $allHeights = range($fromHeight, $toHeight);
        $missingHeights = array_diff($allHeights, $existingHeights);
        $missingCount = count($missingHeights);

        // Group missing heights into ranges for better display
        $missingRanges = $this->groupConsecutiveNumbers($missingHeights);

        return [
            'valid' => false,
            'missing_count' => $missingCount,
            'missing_ranges' => $missingRanges,
            'existing_count' => $existingCount,
            'total_blocks' => $totalBlocks,
        ];
    }

    private function groupConsecutiveNumbers(array $numbers): array
    {
        if (empty($numbers)) {
            return [];
        }

        sort($numbers);
        $ranges = [];
        $start = $numbers[0];
        $end = $numbers[0];

        for ($i = 1; $i < count($numbers); $i++) {
            if ($numbers[$i] === $end + 1) {
                $end = $numbers[$i];
            } else {
                $ranges[] = [
                    'start' => $start,
                    'end' => $end,
                    'count' => $end - $start + 1,
                ];
                $start = $end = $numbers[$i];
            }
        }

        // Add the last range
        $ranges[] = [
            'start' => $start,
            'end' => $end,
            'count' => $end - $start + 1,
        ];

        return $ranges;
    }

    private function processBatch(int $fromHeight, int $toHeight, int $delay): void
    {
        for ($height = $fromHeight; $height <= $toHeight; $height++) {
            // Check for shutdown signal
            if ($this->shouldStop) {
                $this->warn("🛑 Graceful shutdown requested during batch processing at block {$height}.");
                break;
            }

            try {
                $this->processBlock($height);
                $this->processedBlocks++;

                // Advance main progress bar after each block (only if not stopping)
                if ($this->progressBar && ! $this->shouldStop) {
                    $this->progressBar->advance(1);
                }

                // Delay between RPC calls
                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            } catch (\Exception $e) {
                $this->error("❌ Critical error processing block {$height}: ".$e->getMessage());
                $this->error("Stopping sync to prevent data gaps. Fix the issue and restart from block {$height}.");
                Log::error('SyncTransactions: Block processing failed', [
                    'height' => $height,
                    'error' => $e->getMessage(),
                ]);
                throw $e; // Re-throw to stop the sync process
            }
        }
    }

    private function processBlock(int $height): void
    {
        // Get block hash from database
        $blockHash = DB::table('blocks')->where('height', $height)->value('hash');

        if (! $blockHash) {
            $this->warn("Block {$height} not found in database. Run sync:blocks first.");

            return;
        }

        // Get block with full transaction data
        $block = $this->retryRpcCall(fn () => $this->rpc->getBlock($blockHash, 2), "getBlock for {$blockHash}");

        // Skip if no transactions
        if (empty($block['tx'])) {
            return;
        }

        try {
            DB::transaction(function () use ($block, $height) {
                // Process transactions in two passes to handle dependencies

                // First pass: Process all transactions (basic data only)
                foreach ($block['tx'] as $txData) {
                    try {
                        $this->processTransactionBasic($txData, $height, $block['hash'], $block['time'] ?? null);
                    } catch (\Exception $e) {
                        $this->error("Failed to process transaction basic data for {$txData['txid']}: ".$e->getMessage());
                        throw $e;
                    }
                }

                // Second pass: Process inputs/outputs with full dependency resolution
                foreach ($block['tx'] as $txData) {
                    try {
                        $this->processTransactionInputsOutputs($txData['txid'], $txData);
                        $this->processedTransactions++;
                    } catch (\Exception $e) {
                        $this->error("Failed to process transaction I/O for {$txData['txid']}: ".$e->getMessage());
                        throw $e;
                    }
                }
            });

            // Batch recalculate addresses when we reach threshold
            if (count($this->affectedAddresses) >= $this->addressBatchSize) {
                $this->batchRecalculateAddresses();
            }

        } catch (\Exception $e) {
            $this->error("Database transaction failed for block {$height}: ".$e->getMessage());
            throw $e;
        }
    }

    private function processTransactionBasic(array $txData, int $blockHeight, string $blockHash, ?int $blockTime): void
    {
        $txid = $txData['txid'];

        // Skip if transaction already exists (unless forcing)
        if (! $this->option('force') && DB::table('transactions')->where('tx_id', $txid)->exists()) {
            return;
        }

        $isCoinbase = ! empty($txData['vin'][0]['coinbase']);

        // Calculate fee manually (input total - output total)
        $fee = 0;
        if (! $isCoinbase) {
            $fee = $this->calculateTransactionFee($txData);
        }

        // Insert/update transaction with complete data
        try {
            DB::table('transactions')->updateOrInsert(
                ['tx_id' => $txid],
                [
                    'block_height' => $blockHeight,
                    'size' => $txData['size'] ?? 0,
                    'fee' => $fee,
                    'version' => $txData['version'] ?? 1,
                    'locktime' => $txData['locktime'] ?? 0,
                    'is_coinbase' => $isCoinbase,
                ]
            );
        } catch (\Exception $e) {
            $this->error("Failed to insert transaction {$txid} for block {$blockHeight}: ".$e->getMessage());
            throw $e;
        }

        // Process outputs first (so they're available for input resolution)
        $this->processTransactionOutputs($txid, $txData['vout'] ?? []);
    }

    private function processTransactionInputsOutputs(string $txid, array $txData): void
    {
        // Skip if transaction doesn't exist
        if (! DB::table('transactions')->where('tx_id', $txid)->exists()) {
            return;
        }

        $isCoinbase = ! empty($txData['vin'][0]['coinbase']);

        // Process inputs (now that all outputs in the block are available)
        $this->processTransactionInputs($txid, $txData['vin'] ?? []);
    }

    private function processTransactionInputs(string $txId, array $inputs): void
    {
        $inputCount = count($inputs);

        // Clear existing inputs if forcing
        if ($this->option('force')) {
            DB::table('transaction_inputs')->where('tx_id', $txId)->delete();
        }

        // Process inputs in batches for large transactions
        $batchSize = $inputCount > 1000 ? 100 : 500;
        $batches = array_chunk($inputs, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            // Process input batch silently

            foreach ($batch as $index => $input) {
                $isCoinbase = ! empty($input['coinbase']);

                $inputData = [
                    'tx_id' => $txId,
                    'input_index' => $index,
                    'prev_tx_id' => $isCoinbase ? null : $input['txid'],
                    'prev_vout' => $isCoinbase ? null : $input['vout'],
                    'script_sig' => $input['scriptSig']['hex'] ?? null,
                    'sequence' => $input['sequence'] ?? 4294967295,
                    'coinbase_data' => $isCoinbase ? $input['coinbase'] : null,
                ];

                // For non-coinbase inputs, try to resolve the address and amount
                if (! $isCoinbase && isset($input['txid'], $input['vout'])) {
                    $prevOutput = DB::table('transaction_outputs')
                        ->where('tx_id', $input['txid'])
                        ->where('output_index', $input['vout'])
                        ->select('address', 'amount')
                        ->first();

                    if ($prevOutput) {
                        $inputData['address'] = $prevOutput->address;
                        $inputData['amount'] = $prevOutput->amount;

                        // Mark the previous output as spent
                        DB::table('transaction_outputs')
                            ->where('tx_id', $input['txid'])
                            ->where('output_index', $input['vout'])
                            ->update([
                                'is_spent' => true,
                                'spent_by_tx_id' => $txId,
                                'spent_by_input_index' => $index,
                            ]);

                        // Track affected address for batch recalculation
                        $this->affectedAddresses[$prevOutput->address] = true;

                        // Check threshold after each input to process addresses early
                        if (count($this->affectedAddresses) >= $this->addressBatchSize) {
                            $this->batchRecalculateAddresses();
                        }
                    }
                }

                DB::table('transaction_inputs')->updateOrInsert(
                    [
                        'tx_id' => $txId,
                        'input_index' => $index,
                    ],
                    $inputData
                );
            }

            // Add small delay for very large transactions to prevent overwhelming the database
            if ($inputCount > 1000 && $batchIndex < count($batches) - 1) {
                usleep(10000); // 10ms delay between batches
            }
        }
    }

    private function processTransactionOutputs(string $txId, array $outputs): void
    {
        $outputCount = count($outputs);

        // Clear existing outputs if forcing
        if ($this->option('force')) {
            DB::table('transaction_outputs')->where('tx_id', $txId)->delete();
        }

        // Process outputs in batches for large transactions
        $batchSize = $outputCount > 1000 ? 100 : 500;
        $batches = array_chunk($outputs, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            // Process output batch silently

            foreach ($batch as $index => $output) {
                $addresses = $output['scriptPubKey']['addresses'] ?? [];
                $address = ! empty($addresses) ? $addresses[0] : null;
                $scriptType = $output['scriptPubKey']['type'] ?? null;

                // Handle OP_RETURN outputs
                $opReturnData = null;
                $opReturnDecoded = null;
                $opReturnProtocol = null;

                if ($scriptType === 'nulldata') {
                    $opReturnData = $this->extractOpReturnData($output['scriptPubKey']['hex'] ?? '');
                    if ($opReturnData) {
                        $opReturnDecoded = $this->decodeOpReturnData($opReturnData);
                        $opReturnProtocol = $this->detectOpReturnProtocol($opReturnData);
                    }
                }

                $outputData = [
                    'tx_id' => $txId,
                    'output_index' => $index,
                    'address' => $address,
                    'amount' => $output['value'],
                    'script_pub_key' => $output['scriptPubKey']['hex'] ?? '',
                    'script_type' => $scriptType,
                    'op_return_data' => $opReturnData,
                    'op_return_decoded' => $opReturnDecoded,
                    'op_return_protocol' => $opReturnProtocol,
                    'is_spent' => false, // Will be updated when spent
                ];

                DB::table('transaction_outputs')->updateOrInsert(
                    [
                        'tx_id' => $txId,
                        'output_index' => $index,
                    ],
                    $outputData
                );

                // Track affected address for batch recalculation
                if ($address) {
                    $this->affectedAddresses[$address] = true;

                    // Check threshold after each output to process addresses early
                    if (count($this->affectedAddresses) >= $this->addressBatchSize) {
                        $this->batchRecalculateAddresses();
                    }
                }
            }

            // Add small delay for very large transactions to prevent overwhelming the database
            if ($outputCount > 1000 && $batchIndex < count($batches) - 1) {
                usleep(10000); // 10ms delay between batches
            }
        }
    }

    private function retryRpcCall(callable $rpcCall, string $operation, int $maxRetries = 3, int $delayMs = 1000): mixed
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                return $rpcCall();
            } catch (\Exception $e) {
                $lastException = $e;

                if ($attempt < $maxRetries) {
                    usleep($delayMs * 1000);
                    $delayMs *= 2; // Exponential backoff
                }
            }
        }

        throw $lastException;
    }

    private function batchRecalculateAddresses(): void
    {
        $uniqueAddresses = array_keys($this->affectedAddresses);

        if (empty($uniqueAddresses) || $this->shouldStop) {
            return;
        }

        $chunkSize = 50;
        $chunks = array_chunk($uniqueAddresses, $chunkSize);

        // Process addresses in smaller chunks to reduce database load
        foreach ($chunks as $chunkIndex => $addressChunk) {
            foreach ($addressChunk as $address) {
                $this->recalculateAddressBalance($address);
                $this->processedAddresses++;
            }

            // Add small delay between chunks to prevent overwhelming the database
            if ($chunkIndex < count($chunks) - 1) {
                usleep(10000); // 10ms delay between chunks
            }
        }

        // Clear the affected addresses for the next batch
        $this->affectedAddresses = [];
    }

    private function recalculateAddressBalance(string $address): void
    {
        DB::transaction(function () use ($address) {
            // Single query to get output statistics and transaction IDs
            $outputStats = DB::table('transaction_outputs')
                ->selectRaw('
                    SUM(CASE WHEN is_spent = 0 THEN amount ELSE 0 END) as balance,
                    SUM(amount) as total_received,
                    COUNT(DISTINCT tx_id) as output_tx_count
                ')
                ->where('address', $address)
                ->first();

            // Single query to get input statistics and transaction IDs
            $inputStats = DB::table('transaction_inputs')
                ->selectRaw('
                    SUM(amount) as total_sent,
                    COUNT(DISTINCT tx_id) as input_tx_count
                ')
                ->where('address', $address)
                ->whereNotNull('amount')
                ->first();

            // Single query to get timestamp information
            $timeStats = DB::table('transaction_outputs')
                ->join('transactions', 'transaction_outputs.tx_id', '=', 'transactions.tx_id')
                ->join('blocks', 'transactions.block_height', '=', 'blocks.height')
                ->selectRaw('
                    MIN(blocks.created_at) as first_seen,
                    MAX(blocks.created_at) as last_activity
                ')
                ->where('transaction_outputs.address', $address)
                ->first();

            // Calculate unique transaction count (approximate - may have slight overlap)
            $txCount = ($outputStats->output_tx_count ?? 0) + ($inputStats->input_tx_count ?? 0);

            DB::table('address_balances')->updateOrInsert(
                ['address' => $address],
                [
                    'balance' => $outputStats->balance ?? 0,
                    'total_received' => $outputStats->total_received ?? 0,
                    'total_sent' => $inputStats->total_sent ?? 0,
                    'tx_count' => $txCount,
                    'first_seen' => $timeStats->first_seen,
                    'last_activity' => $timeStats->last_activity,
                ]
            );
        });
    }

    private function extractOpReturnData(string $scriptHex): ?string
    {
        if (empty($scriptHex)) {
            return null;
        }

        // OP_RETURN scripts start with 6a (OP_RETURN opcode)
        if (! str_starts_with($scriptHex, '6a')) {
            return null;
        }

        // Remove OP_RETURN opcode (6a)
        $data = substr($scriptHex, 2);

        // Next byte is the data length
        if (strlen($data) < 2) {
            return null;
        }

        $lengthHex = substr($data, 0, 2);
        $length = hexdec($lengthHex);

        // Extract the actual data
        $opReturnData = substr($data, 2, $length * 2);

        return $opReturnData ?: null;
    }

    /**
     * Attempt to decode OP_RETURN data as UTF-8 text
     */
    private function decodeOpReturnData(string $hexData): ?string
    {
        if (empty($hexData)) {
            return null;
        }

        // Convert hex to binary
        $binary = hex2bin($hexData);
        if ($binary === false) {
            return null;
        }

        // Check if it's valid UTF-8
        if (mb_check_encoding($binary, 'UTF-8')) {
            // Remove null bytes and control characters for display
            $decoded = preg_replace('/[\x00-\x1F\x7F]/', '', $binary);

            return trim($decoded) ?: null;
        }

        return null;
    }

    private function detectOpReturnProtocol(string $hexData): ?string
    {
        if (empty($hexData)) {
            return null;
        }

        // Common protocol prefixes (in hex)
        $protocols = [
            '4f4d4e49' => 'omni',           // "OMNI"
            '434f554e' => 'counterparty',   // "COUN" (Counterparty)
            '5350' => 'simple_ledger',      // "SP" (Simple Ledger Protocol)
            '534c50' => 'simple_ledger',    // "SLP" (Simple Ledger Protocol)
            '4f41' => 'open_assets',        // "OA" (Open Assets)
            '434852' => 'chronicled',       // "CHR" (Chronicled)
            '4d47' => 'mastercoin',         // "MG" (Mastercoin/Omni)
            '5354' => 'storj',              // "ST" (Storj)
            '4343' => 'colored_coins',      // "CC" (Colored Coins)
        ];

        foreach ($protocols as $prefix => $protocol) {
            if (str_starts_with(strtoupper($hexData), strtoupper($prefix))) {
                return $protocol;
            }
        }

        // Check for common text patterns
        $decoded = $this->decodeOpReturnData($hexData);
        if ($decoded) {
            // Look for common patterns in decoded text
            if (preg_match('/^(http|https):\/\//i', $decoded)) {
                return 'url';
            }
            if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $decoded)) {
                return 'email';
            }
            if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $decoded)) {
                return 'uuid';
            }
            if (strlen($decoded) > 10 && ctype_print($decoded)) {
                return 'text';
            }
        }

        return 'unknown';
    }

    private function calculateTransactionFee(array $txData): string
    {
        // Calculate total output value in satoshis (integer arithmetic)
        $totalOutputSatoshis = 0;
        foreach ($txData['vout'] ?? [] as $output) {
            $totalOutputSatoshis += $this->toSatoshis($output['value']);
        }

        // Calculate total input value by looking up previous outputs
        $totalInputSatoshis = 0;
        foreach ($txData['vin'] ?? [] as $input) {
            if (isset($input['coinbase'])) {
                continue; // Skip coinbase inputs
            }

            if (isset($input['txid'], $input['vout'])) {
                // Look up the previous output value from database
                $prevOutput = DB::table('transaction_outputs')
                    ->where('tx_id', $input['txid'])
                    ->where('output_index', $input['vout'])
                    ->value('amount');

                if ($prevOutput !== null) {
                    $totalInputSatoshis += $this->toSatoshis($prevOutput);
                } else {
                    // If we can't find the input, try to get it from RPC
                    try {
                        $prevTx = $this->retryRpcCall(fn () => $this->rpc->getRawTransaction($input['txid'], true), "getRawTransaction for {$input['txid']}");
                        if (isset($prevTx['vout'][$input['vout']]['value'])) {
                            $totalInputSatoshis += $this->toSatoshis($prevTx['vout'][$input['vout']]['value']);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Could not get input value for {$input['txid']}:{$input['vout']}: ".$e->getMessage());
                    }
                }
            }
        }

        // Fee is the difference between inputs and outputs (in satoshis)
        $feeSatoshis = max(0, $totalInputSatoshis - $totalOutputSatoshis);

        // Convert back to decimal format
        return $this->fromSatoshis($feeSatoshis);
    }

    private function toSatoshis($amount): int
    {
        // Handle scientific notation by using bcmath first
        $amountStr = (string) $amount;
        if (stripos($amountStr, 'e') !== false) {
            // Convert scientific notation to decimal using bcmath
            $decimal = bcadd('0', $amountStr, 8);
        } else {
            $decimal = $amountStr;
        }

        // Multiply by 100,000,000 using bcmath to avoid precision loss
        $satoshiStr = bcmul($decimal, '100000000', 0);

        return (int) $satoshiStr;
    }

    /**
     * Convert satoshis (integer) back to decimal format
     */
    private function fromSatoshis(int $satoshis): string
    {
        // Use bcmath to divide by 100,000,000 and get exact decimal
        return bcdiv((string) $satoshis, '100000000', 8);
    }

    private function displaySummary(): void
    {
        // Final batch recalculation for any remaining addresses
        $this->batchRecalculateAddresses();

        $this->info('✅ Transaction sync completed successfully!');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Blocks Processed', number_format($this->processedBlocks)],
                ['Transactions Processed', number_format($this->processedTransactions)],
                ['Addresses Updated', number_format($this->processedAddresses)],
            ]
        );

        // Show database statistics
        $totalTxs = DB::table('transactions')->count();
        $totalAddresses = DB::table('address_balances')->count();
        $totalBalance = DB::table('address_balances')->sum('balance');
        $opReturnCount = DB::table('transaction_outputs')->whereNotNull('op_return_data')->count();

        $this->info('📊 Database Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Transactions', number_format($totalTxs)],
                ['Total Addresses', number_format($totalAddresses)],
                ['OP_RETURN Outputs', number_format($opReturnCount)],
                ['Total PEPE Supply', number_format($totalBalance, 8).' PEPE'],
            ]
        );

        // Show OP_RETURN protocol breakdown
        $protocolStats = DB::table('transaction_outputs')
            ->whereNotNull('op_return_protocol')
            ->selectRaw('op_return_protocol, COUNT(*) as count')
            ->groupBy('op_return_protocol')
            ->orderByDesc('count')
            ->get();

        if ($protocolStats->isNotEmpty()) {
            $this->info('🔍 OP_RETURN Protocol Breakdown:');
            $this->table(
                ['Protocol', 'Count'],
                $protocolStats->map(fn ($stat) => [
                    ucfirst($stat->op_return_protocol),
                    number_format($stat->count),
                ])->toArray()
            );
        }
    }
}
