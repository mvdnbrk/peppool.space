<x-layout title="Pepecoin Price - peppool.space" og_image="pepecoin-price.png">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Total Supply Card (only if cached) -->
        @if(!is_null($supply))
            <x-stat-card aria-label="Current total supply" icon-bg="bg-green-700" label="Total Supply">
                <x-slot:icon>
                    <x-icon-supply class="w-5 h-5 text-white" />
                </x-slot:icon>
                <span class="text-2xl">
                    @php
                        $supplyInt = (int) $supply;
                        $billions = intdiv($supplyInt, 1000000000);
                        $billionsText = strtolower(\Illuminate\Support\Number::format($billions, maxPrecision: 0) . ' Billion');
                    @endphp
                    {{ \Illuminate\Support\Number::format($supplyInt, maxPrecision: 0) }}
                    <span class="ml-2 text-gray-700 dark:text-gray-200 text-base font-semibold">{{ $billionsText }}</span>
                </span>
            </x-stat-card>
        @endif

        <!-- Current Price Card -->
        <x-stat-card aria-label="Current price" icon-bg="bg-green-700" label="Price">
            <x-slot:icon>
                <x-icon-pep-currency-sign class="w-5 h-5 text-white" />
            </x-slot:icon>
            <span class="text-2xl">
                @php
                    $currencySymbol = $currency === 'EUR' ? '€' : '$';
                    // Format up to 6 decimals, strip trailing zeros and a dangling decimal point
                    $formattedPrice = rtrim(rtrim(number_format($price, 6, '.', ''), '0'), '.');
                @endphp
                {{ $currencySymbol }}&nbsp;{{ $formattedPrice }}
                <span class="ml-2 text-gray-500 text-base">{{ $currency }}</span>
            </span>
        </x-stat-card>
    </div>
    <!-- Price Chart -->
    <div class="mb-8">
        @php
            $signedChartUrl = URL::signedRoute('api.chart.prices', [
                'period' => request('period', '24h'),
                'currency' => request('currency', 'USD'),
            ]);
            // Convert to relative path to guarantee same-origin for fetch
            $relativeSignedUrl = \Illuminate\Support\Str::after($signedChartUrl, url('/'));
            $chartProps = [ 'apiUrl' => $relativeSignedUrl ];
        @endphp
        <div
            data-vue="price-chart"
            data-props='@json($chartProps)'
        ></div>
    </div>
</x-layout>
