<x-layout title="Peppool.space - Real-time Pepecoin Blockchain Explorer" :network="$network">

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
        <div class="bg-white dark:bg-gray-800 rounded-b-lg shadow overflow-hidden">
            <div class="relative">
                <x-wave color="text-green-700" flip="true" class="-mt-px rotate-180 bg-gray-50 dark:bg-gray-900"/>
                <div class="px-6 py-2 bg-green-700">
                    <h2 class="text-lg font-ubuntu font-bold italic text-white">Latest Blocks</h2>
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
                    <h2 class="text-lg font-ubuntu font-bold italic text-white">Mempool Transactions</h2>
                </div>
                <x-wave color="text-green-700" class="-mt-px"/>
            </div>
            <div class="overflow-hidden">
                <div
                    x-data="mempoolWidget({ initialTxids: @js($mempoolTransactions), apiUrl: '{{ route('api.mempool.txids') }}', intervalMs: 10000 })"
                    x-init="init()"
                >
                    <template x-for="txid in txids" :key="txid">
                        <a
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            :href="`/tx/${txid}`"
                            class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors will-change-transform"
                        >
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                        <x-icon-refresh class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    </div>
                                </div>
                                <div class="ml-4 min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="txid.substring(0,16) + '...' + txid.substring(txid.length-8)"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Unconfirmed</p>
                                </div>
                            </div>
                        </a>
                    </template>
                    <div class="px-6 py-8 text-center" x-show="txids.length === 0">
                        <p class="text-gray-500 dark:text-gray-400">no transactions in mempool</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
