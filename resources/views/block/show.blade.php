<x-layout title="Block {{ number_format($blockHeight) }} - peppool.space">
    @if(isset($error))
        <!-- Error State -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-icon-exclamation-circle class="w-5 h-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">{{ $error }}</h3>
                </div>
            </div>
        </div>
    @else
        <!-- Block Navigation -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-2">
                @if($prevBlockHash)
                    <a href="{{ route('block.show', $blockHeight - 1) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        ← Previous Block
                    </a>
                @endif
            </div>
            <div class="flex space-x-2">
                @if($nextBlockHash)
                    <a href="{{ route('block.show', $blockHeight + 1) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next Block →
                    </a>
                @endif
            </div>
        </div>

        <!-- Block Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Block {{ number_format($blockHeight) }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Block Hash</h3>
                    <p class="text-sm font-mono bg-gray-100 p-2 rounded break-all">{{ $blockHash }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Previous Block Hash</h3>
                    <p class="text-sm font-mono bg-gray-100 p-2 rounded break-all">
                        @if(isset($block['previousblockhash']))
                            <a href="{{ route('block.show', $block['previousblockhash']) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $block['previousblockhash'] }}
                            </a>
                        @else
                            Genesis Block
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Block Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                            <x-icon-clock class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Block Time</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ date('Y-m-d H:i:s', $block['time']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <x-icon-document-text class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transactions</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ count($block['tx'] ?? []) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                            <x-icon-hammer class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Difficulty</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ format_difficulty($block['difficulty']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <x-icon-database class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($block['size'] ?? 0) }} bytes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Transactions ({{ count($block['tx'] ?? []) }})</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($block['tx'] ?? [] as $index => $tx)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $index === 0 ? 'Coinbase' : 'Transaction' }}
                                    </span>
                                    <a href="{{ route('transaction.show', is_string($tx) ? $tx : $tx['txid']) }}" class="text-sm font-mono text-blue-600 hover:text-blue-800 break-all">
                                        {{ is_string($tx) ? $tx : $tx['txid'] }}
                                    </a>
                                </div>
                                @if(is_array($tx) && isset($tx['vout']))
                                    <div class="mt-2 text-sm text-gray-500">
                                        {{ count($tx['vin'] ?? []) }} {{ Str::plural('input', count($tx['vin'] ?? [])) }}, {{ count($tx['vout'] ?? []) }} {{ Str::plural('output', count($tx['vout'] ?? [])) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500">
                        No transactions found
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</x-layout>
