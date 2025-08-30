@props([
    'initialTxids' => [],
    'apiUrl' => '',
    'intervalMs' => 10000,
])

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
            x-data="mempoolWidget({ initialTxids: @js($initialTxids), apiUrl: '{{ $apiUrl }}', intervalMs: {{ $intervalMs }} })"
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
