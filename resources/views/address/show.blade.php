<x-layout
    :title="'Address ' . $address"
    :og_description="'Details for Pepecoin address ' . $address . ': balance, totals and recent transactions on peppool.space.'"
    og_image="pepecoin-address.png"
>
<div class="space-y-6">
    <!-- Address Header -->
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white break-all">{{ $address }}</h1>
                    @if($isMine ?? false)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 mt-2">
                            Your Address
                        </span>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex flex-wrap gap-4">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Balance</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                @if($balance !== null)
                                    {{ number_format($balance, 8) }} <span class="text-sm text-gray-500 dark:text-gray-400">PEPE</span>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Unknown</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Received</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                @if($totalReceived !== null)
                                    {{ number_format($totalReceived, 8) }} <span class="text-sm text-gray-500 dark:text-gray-400">PEPE</span>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Unknown</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($showComingSoon) && $showComingSoon)
        <x-notification type="coming-soon">
            <div class="font-medium">Full address transaction history is coming soon!</div>
            <div class="mt-1 text-sm">We're working on implementing comprehensive address lookups - check back soon for this feature.</div>
        </x-notification>
    @endif

    @if(isset($error))
        <x-notification type="error">
            {{ $error }}
        </x-notification>
    @endif

    <!-- Transactions -->
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transactions</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @php
                $transactions = collect($txs ?? [])->sortByDesc('time');
            @endphp
            @forelse($transactions as $tx)
                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('transaction.show', $tx['txid']) }}" class="font-mono text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 break-all">
                                {{ $tx['txid'] }}
                            </a>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($tx['time'] ?? $tx['timereceived'] ?? false)
                                    @php $ts = \Carbon\Carbon::createFromTimestamp($tx['timereceived'] ?? $tx['time']); @endphp
                                    <timestamp
                                        x-data="timestamp"
                                        datetime="{{ $ts->toAtomString() }}"
                                        x-text="relativeTime"
                                        title="{{ $ts->format('Y-m-d H:i:s') }}"></timestamp>
                                @else
                                    Unconfirmed
                                @endif
                                @if(isset($tx['confirmations']))
                                    <span class="ml-2">
                                        {{ $tx['confirmations'] }} confirmation{{ $tx['confirmations'] != 1 ? 's' : '' }}
                                    </span>
                                @endif
                                @if($tx['is_coinbase'] ?? false)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 ml-2">
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
                <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    No transactions found for this address
                </div>
            @endforelse
        </div>
    </div>
</div>
</x-layout>
