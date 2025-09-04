<x-layout title="Block {{ number_format($blockHeight) }} - peppool.space" :og_description="'Details for Pepecoin block ' . number_format($blockHeight) . ': hash, transactions, size, time and more on peppool.space.'">
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
                    <a href="{{ route('block.show', $blockHeight - 1) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-700 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                        ← Previous Block
                    </a>
                @endif
            </div>
            <div class="flex space-x-2">
                @if($nextBlockHash)
                    <a href="{{ route('block.show', $blockHeight + 1) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-700 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                        Next Block →
                    </a>
                @endif
            </div>
        </div>

        <!-- Block Header -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Block {{ number_format($blockHeight) }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Block Hash</h3>
                    <p class="text-sm font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">{{ $blockHash }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Previous Block Hash</h3>
                    <p class="text-sm font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                        @if(isset($block['previousblockhash']))
                            <a href="{{ route('block.show', $block['previousblockhash']) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
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
            <x-stat-card icon-bg="bg-blue-500" label="Mined">
                <x-slot:icon>
                    <x-icon-clock class="w-5 h-5 text-white" />
                </x-slot:icon>
                <timestamp
                    x-data="timestamp"
                    datetime="{{ \Carbon\Carbon::createFromTimestamp($block['time'])->toAtomString() }}"
                    x-text="relativeTime"
                    title="{{ date('Y-m-d H:i:s', $block['time']) }}"
                    class="text-lg font-bold text-gray-900 dark:text-white"></timestamp>
            </x-stat-card>

            <x-stat-card icon-bg="bg-green-500" label="Transactions">
                <x-slot:icon>
                    <x-icon-document-text class="w-5 h-5 text-white" />
                </x-slot:icon>
                <span class="text-lg">{{ count($block['tx'] ?? []) }}</span>
            </x-stat-card>

            <x-stat-card icon-bg="bg-purple-500" label="Difficulty">
                <x-slot:icon>
                    <x-icon-hammer class="w-5 h-5 text-white" />
                </x-slot:icon>
                <span class="text-lg">{{ format_difficulty($block['difficulty']) }}</span>
            </x-stat-card>

            <x-stat-card icon-bg="bg-orange-500" label="Size">
                <x-slot:icon>
                    <x-icon-database class="w-5 h-5 text-white" />
                </x-slot:icon>
                <span class="text-lg">{{ number_format($block['size'] ?? 0) }} bytes</span>
            </x-stat-card>
        </div>

        <!-- Transactions -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transactions ({{ count($block['tx'] ?? []) }})</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($block['tx'] ?? [] as $index => $tx)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $index === 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                        {{ $index === 0 ? 'Coinbase' : 'Transaction' }}
                                    </span>
                                    <a href="{{ route('transaction.show', is_string($tx) ? $tx : $tx['txid']) }}" class="text-sm font-mono text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 break-all">
                                        {{ is_string($tx) ? $tx : $tx['txid'] }}
                                    </a>
                                </div>
                                @if(is_array($tx) && isset($tx['vout']))
                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ count($tx['vin'] ?? []) }} {{ Str::plural('input', count($tx['vin'] ?? [])) }}, {{ count($tx['vout'] ?? []) }} {{ Str::plural('output', count($tx['vout'] ?? [])) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        No transactions found
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</x-layout>
