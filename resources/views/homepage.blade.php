<x-layout
    title="Peppool.space - Real-time Pepecoin Blockchain Explorer"
    :network="$network"
    og_image="default-card-large.png"
    og_description="Fast, real-time Pepecoin blockchain explorer. Search blocks, transactions and addresses with live mempool, latest blocks, and network stats.">

    <!-- Global Search -->
    <x-search class="mb-8" />

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
        <!-- Block Height Card -->
        <x-stat-card aria-label="Current block height: {{ $blockHeight }}" icon-bg="bg-blue-500" label="Block">
            <x-slot:icon>
                <x-icon-cube class="w-5 h-5 text-white" />
            </x-slot:icon>
            <span class="text-2xl" id="current-block-height">{{ $blockHeight }}</span>
        </x-stat-card>

        <!-- Mempool Card -->
        <x-stat-card aria-label="Mempool: {{ number_format($mempool['size'] ?? 0) }} transactions waiting" icon-bg="bg-green-500" label="Mempool">
            <x-slot:icon>
                <x-icon-refresh class="w-5 h-5 text-white" />
            </x-slot:icon>
            <span class="text-2xl"><span id="mempool-count">{{ number_format($mempool['size'] ?? 0) }}</span> txs</span>
        </x-stat-card>

        <!-- Difficulty Card -->
        <x-stat-card aria-label="Current network difficulty: {{ $difficulty }}" icon-bg="bg-purple-500" label="Difficulty">
            <x-slot:icon>
                <x-icon-hammer class="w-5 h-5 text-white" />
            </x-slot:icon>
            <span class="text-2xl">{{ $difficulty }}</span>
        </x-stat-card>

        <!-- Hashrate Card -->
        <x-stat-card aria-label="Current network hashrate: {{ $hashrate }}" icon-bg="bg-cyan-500" label="Network">
            <x-slot:icon>
                <x-icon-share class="w-5 h-5 text-white" />
            </x-slot:icon>
            <span class="text-2xl">{{ $hashrate }}</span>
        </x-stat-card>

        <!-- Chain Size Card -->
        <x-stat-card aria-label="Blockchain size: {{ $chainSize }}" icon-bg="bg-orange-500" label="Chain Size">
            <x-slot:icon>
                <x-icon-database class="w-5 h-5 text-white" />
            </x-slot:icon>
            <span class="text-2xl">{{ $chainSize }}</span>
        </x-stat-card>
    </div>

    <!-- Latest Blocks and Mempool -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Latest Blocks -->
        <x-latest-blocks :latest-blocks="$latestBlocks" />

        <!-- Mempool Transactions -->
        <x-mempool-transactions
            :initial-txids="$mempoolTransactions"
            api-url="{{ route('api.mempool.txids') }}"
            :interval-ms="10000"
        />
    </div>
</x-layout>
