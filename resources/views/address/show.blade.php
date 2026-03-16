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
        <h1 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-4 break-all">
            {{ $address }}
        </h1>
        @if($isMine ?? false)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                Your Address
            </span>
        @endif
    </div>

    <!-- Address Stats -->
    <script type="application/json" id="address-stats-data">
        {!! json_encode([
            'txCount' => $txCount,
            'totalReceived' => $totalReceived,
            'totalSent' => $totalSent,
            'balance' => $balance,
        ]) !!}
    </script>

    <div id="address-stats" data-vue="address-stats" data-props='{ "address": "{{ $address }}" }'>
    </div>

    <!-- Transactions -->

    <script type="application/json" id="address-transactions-data">
        {!! json_encode([
            'address' => $address,
            'transactions' => $transactions,
            'perPage' => $perPage,
            'total' => $txCount,
            'nextAfter' => $nextAfter,
            'after' => $after,
            'txRoute' => route('transaction.show', ['txid' => '__TXID__'])
        ]) !!}
    </script>

    <div id="address-transactions" data-vue="address-transactions">
    </div>
</div>
</x-layout>
