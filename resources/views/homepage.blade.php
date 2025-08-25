<x-layout title="Peppool.space - Real-time Pepecoin Blockchain Explorer" :network="$network">
    <!-- Global Search -->
    <x-search class="mb-8" />

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
        <!-- Block Height Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Block</p>
                    <p class="text-2xl font-bold text-gray-900" id="current-block-height">
                        {{ $blockHeight }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Mempool Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Mempool</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span id="mempool-count">{{ number_format($mempool['size'] ?? 0) }}</span> txs
                    </p>
                </div>
            </div>
        </div>

        <!-- Difficulty Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.79,10.62L3.5,21.9L2.1,20.5L13.38,9.21L14.79,10.62M19.27,7.73L19.86,7.14L19.07,6.35L19.71,5.71L18.29,4.29L17.65,4.93L16.86,4.14L16.27,4.73C14.53,3.31 12.57,2.17 10.47,1.37L9.64,3.16C11.39,4.08 13,5.19 14.5,6.5L14,7L17,10L17.5,9.5C18.81,11 19.92,12.61 20.84,14.36L22.63,13.53C21.83,11.43 20.69,9.47 19.27,7.73Z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Difficulty</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $difficulty }}</p>
                </div>
            </div>
        </div>

        <!-- Hashrate Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-cyan-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="18" cy="5" r="3" stroke-width="2"/>
                            <circle cx="18" cy="19" r="3" stroke-width="2"/>
                            <circle cx="6" cy="12" r="3" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.408 6.51199L8.59436 10.4866M15.408 17.488L8.59436 13.5134"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Network</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $hashrate }}</p>
                </div>
            </div>
        </div>

        <!-- Chain Size Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Chain Size</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $chainSize }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Blocks and Mempool -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Latest Blocks -->
        <div class="bg-white rounded-b-lg shadow overflow-hidden">
            <div class="relative">
                <x-wave color="text-green-700" flip="true" class="-mt-px rotate-180 bg-gray-50"/>
                <div class="px-6 py-2 bg-green-700">
                    <h2 class="text-lg font-semibold text-white">Latest Blocks</h2>
                </div>
                <x-wave color="text-green-700" class="-mt-px"/>
            </div>
            <div class="overflow-hidden">
                @forelse($latestBlocks as $block)
                    <a href="{{ route('block.show', $block['height']) }}" class="block px-6 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="px-3 py-1 bg-green-100 rounded-md flex items-center justify-center">
                                        <span class="text-sm font-semibold text-green-600">{{ $block['height'] }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ substr($block['hash'], 0, 16) }}...{{ substr($block['hash'], -8) }}
                                    </p>
                                    <timestamp datetime="{{ \Carbon\Carbon::createFromTimestamp($block['time'])->toAtomString() }}" class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::createFromTimestamp($block['time'])->diffForHumans() }}
                                    </timestamp>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $block['tx_count'] }} txs</p>
                                <p class="text-sm text-gray-500">{{ number_format($block['size'] / 1024, 1) }} KB</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-gray-500">No blocks available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Mempool Transactions -->
        <div class="bg-white rounded-b-lg shadow overflow-hidden">
            <div class="relative">
                <x-wave color="text-green-700" class="-mt-px rotate-180 bg-gray-50"/>
                <div class="px-6 py-2 bg-green-700">
                    <h2 class="text-lg font-semibold text-white">Mempool Transactions</h2>
                </div>
                <x-wave color="text-green-700" class="-mt-px"/>
            </div>
            <div class="overflow-hidden">
                @foreach($mempoolTransactions as $txid)
                    <a href="{{ route('transaction.show', $txid) }}" class="block px-6 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ substr($txid, 0, 16) }}...{{ substr($txid, -8) }}
                                </p>
                                <p class="text-sm text-gray-500">Unconfirmed</p>
                            </div>
                        </div>
                    </a>
                @endforeach
                <div class="px-6 py-8 text-center {{ !empty($mempoolTransactions) ? 'invisible' : '' }}">
                    <p class="text-gray-500">no transactions in mempool</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
