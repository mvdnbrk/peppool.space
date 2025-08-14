<x-layout title="Block {{ number_format($blockHeight) }} - peppool.space">
    @if(isset($error))
        <!-- Error State -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
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
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Block Time</p>
                        <p class="text-lg font-bold text-gray-900">{{ date('Y-m-d H:i:s', $block['time']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Transactions</p>
                        <p class="text-lg font-bold text-gray-900">{{ count($block['tx'] ?? []) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Size</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($block['size'] ?? 0) }} bytes</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Difficulty</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($block['difficulty'] ?? 0, 2) }}</p>
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
                                        {{ count($tx['vin'] ?? []) }} input(s), {{ count($tx['vout'] ?? []) }} output(s)
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
