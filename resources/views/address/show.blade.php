<x-layout
    :title="'Address ' . $address"
    breadcrumb="Address"
>
<div class="space-y-6">
    <!-- Address Header -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 break-all">{{ $address }}</h1>
                    @if($isMine ?? false)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                            Your Address
                        </span>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex flex-wrap gap-4">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500">Balance</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($balance ?? 0, 8) }} <span class="text-sm text-gray-500">PEPE</span></p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500">Total Received</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($totalReceived ?? 0, 8) }} <span class="text-sm text-gray-500">PEPE</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Transactions -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Transactions</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @php
                $transactions = collect($txs ?? [])->sortByDesc('time');
            @endphp
            @forelse($transactions as $tx)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('transaction.show', $tx['txid']) }}" class="font-mono text-blue-600 hover:text-blue-800 break-all">
                                {{ $tx['txid'] }}
                            </a>
                            <div class="mt-1 text-sm text-gray-500">
                                @if($tx['time'])
                                    {{ \Carbon\Carbon::createFromTimestamp($tx['time'])->toDateTimeString() }}
                                @else
                                    Unconfirmed
                                @endif
                                @if(isset($tx['confirmations']))
                                    <span class="ml-2">
                                        {{ $tx['confirmations'] }} confirmation{{ $tx['confirmations'] != 1 ? 's' : '' }}
                                    </span>
                                @endif
                                @if($tx['is_coinbase'] ?? false)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                        Coinbase
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 text-sm font-medium text-right">
                            @if($tx['is_incoming'])
                                <span class="text-green-600">+{{ number_format($tx['amount'], 8) }} PEPE</span>
                            @else
                                <span class="text-red-600">-{{ number_format($tx['amount'], 8) }} PEPE</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-4 text-center text-gray-500">
                    No transactions found for this address
                </div>
            @endforelse
        </div>
    </div>
</div>
</x-layout>
