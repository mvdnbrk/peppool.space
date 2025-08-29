<x-layout title="Peppool.space - Real-time Pepecoin Blockchain Explorer" :network="$network">

    <!-- Global Search -->
    <x-search class="mb-8" />

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
        <!-- Block Height Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" aria-label="Current block height: {{ $blockHeight }}" role="status">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                        <x-icon-cube class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Block</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="current-block-height">
                        {{ $blockHeight }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Mempool Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" aria-label="Mempool: {{ number_format($mempool['size'] ?? 0) }} transactions waiting" role="status">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <x-icon-refresh class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mempool</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        <span id="mempool-count">{{ number_format($mempool['size'] ?? 0) }}</span> txs
                    </p>
                </div>
            </div>
        </div>

        <!-- Difficulty Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" aria-label="Current network difficulty: {{ $difficulty }}" role="status">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                        <x-icon-hammer class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Difficulty</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $difficulty }}</p>
                </div>
            </div>
        </div>

        <!-- Hashrate Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" aria-label="Current network hashrate: {{ $hashrate }}" role="status">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-cyan-500 rounded-xl flex items-center justify-center">
                        <x-icon-share class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Network</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $hashrate }}</p>
                </div>
            </div>
        </div>

        <!-- Chain Size Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" aria-label="Blockchain size: {{ $chainSize }}" role="status">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                        <x-icon-database class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Chain Size</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $chainSize }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Blocks and Mempool -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Latest Blocks -->
        <div class="bg-white dark:bg-gray-800 rounded-b-lg shadow overflow-hidden">
            <div class="relative">
                <x-wave color="text-green-700" flip="true" class="-mt-px rotate-180 bg-gray-50 dark:bg-gray-900"/>
                <div class="px-6 py-2 bg-green-700">
                    <h2 class="text-lg font-semibold text-white">Latest Blocks</h2>
                </div>
                <x-wave color="text-green-700" class="-mt-px"/>
            </div>
            <div class="overflow-hidden">
                @forelse($latestBlocks as $block)
                    <a href="{{ route('block.show', $block['height']) }}" class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="px-3 py-1 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $block['height'] }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ substr($block['hash'], 0, 16) }}...{{ substr($block['hash'], -8) }}
                                    </p>
                                    <timestamp
                                        x-data="timestamp"
                                        datetime="{{ \Carbon\Carbon::createFromTimestamp($block['time'])->toAtomString() }}"
                                        x-text="relativeTime"
                                        class="text-sm text-gray-500 dark:text-gray-400"></timestamp>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $block['tx_count'] }} txs</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($block['size'] / 1024, 1) }} KB</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">No blocks available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Mempool Transactions -->
        <div class="bg-white dark:bg-gray-800 rounded-b-lg shadow overflow-hidden">
            <div class="relative">
                <x-wave color="text-green-700" class="-mt-px rotate-180 bg-gray-50 dark:bg-gray-900"/>
                <div class="px-6 py-2 bg-green-700">
                    <h2 class="text-lg font-semibold text-white">Mempool Transactions</h2>
                </div>
                <x-wave color="text-green-700" class="-mt-px"/>
            </div>
            <div class="overflow-hidden">
                @foreach($mempoolTransactions as $txid)
                    <a href="{{ route('transaction.show', $txid) }}" class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <x-icon-refresh class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ substr($txid, 0, 16) }}...{{ substr($txid, -8) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Unconfirmed</p>
                            </div>
                        </div>
                    </a>
                @endforeach
                <div class="px-6 py-8 text-center {{ $mempoolTransactions->isEmpty() ? 'visible' : 'invisible' }}">
                    <p class="text-gray-500 dark:text-gray-400">no transactions in mempool</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
