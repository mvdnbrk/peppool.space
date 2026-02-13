<x-layout
    :title="'Address ' . $address"
    :og_description="'Details for Pepecoin address ' . $address . ': balance, totals and recent transactions on peppool.space.'"
    og_image="pepecoin-address.png"
>
<div class="space-y-6">
    <!-- Address Header -->
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
            Pepe Wallet address
        </h3>
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-4 break-all">
            {{ $address }}
        </h1>
        @if($isMine ?? false)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                Your Address
            </span>
        @endif
    </div>

    <!-- Address Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-stat-card icon-bg="bg-blue-500" label="Transactions">
            <x-slot:icon>
                <x-icon-document-text class="w-5 h-5 text-white" />
            </x-slot:icon>
            {{ number_format($txCount ?? 0) }}
        </x-stat-card>

        <x-stat-card icon-bg="bg-green-500" label="Total PEPE Received">
            <x-slot:icon>
                <x-icon-arrow-down class="w-5 h-5 text-white" />
            </x-slot:icon>
            @if($totalReceived !== null)
                <x-pepe-amount :amount="$totalReceived" class="text-green-600" />
            @else
                <span class="text-sm text-gray-400 dark:text-gray-500">Unknown</span>
            @endif
        </x-stat-card>

        <x-stat-card icon-bg="bg-red-500" label="Total PEPE Sent">
            <x-slot:icon>
                <x-icon-arrow-up class="w-5 h-5 text-white" />
            </x-slot:icon>
            @if(isset($totalSent) && $totalSent !== null)
                <x-pepe-amount :amount="$totalSent" />
            @else
                <span class="text-sm text-gray-400 dark:text-gray-500">Unknown</span>
            @endif
        </x-stat-card>

        <x-stat-card icon-bg="bg-green-500" label="PEPE Balance">
            <x-slot:icon>
                <x-icon-pep-currency-sign class="w-5 h-5 text-white" />
            </x-slot:icon>
            @if($balance !== null)
                <x-pepe-amount :amount="$balance" />
            @else
                <span class="text-sm text-gray-400 dark:text-gray-500">Unknown</span>
            @endif
        </x-stat-card>
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
    @if(isset($transactions))

    <script type="application/json" id="address-transactions-data">
        {!! json_encode([
            'address' => $address,
            'transactions' => $transactions->items(),
            'currentPage' => $transactions->currentPage(),
            'perPage' => $transactions->perPage(),
            'total' => $transactions->total(),
            'lastPage' => $transactions->lastPage(),
            'txRoute' => route('transaction.show', ['txid' => '__TXID__'])
        ]) !!}
    </script>

    <div id="address-transactions" data-vue="address-transactions">
    </div>
    @else
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transactions</h2>
        </div>
        <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
            No transactions available.
        </div>
    </div>
    @endif
</div>
</x-layout>
