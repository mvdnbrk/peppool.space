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
            data-vue="mempool-transactions" 
            data-props="{{ json_encode([
                'initialTxids' => $initialTxids,
                'apiUrl' => $apiUrl,
                'intervalMs' => $intervalMs
            ]) }}"
        ></div>
    </div>
</div>
