<x-layout title="Pepecoin Price - peppool.space" og_image="pepecoin-price.png" og_description="Live Pepecoin price chart with 24h, 7D and 30D ranges, market cap and total supply. Updated frequently on peppool.space." :showPepePrice="false">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

        <!-- Market Cap Card -->
        @if(!is_null($marketCap))
            <x-stat-card aria-label="Current market cap" icon-bg="bg-green-700" label="Market Cap">
                <x-slot:icon>
                    <x-icon-chart class="w-5 h-5 text-white" />
                </x-slot:icon>
                <span class="text-2xl">
                    @php
                        $currencySymbol = $currency === 'EUR' ? '€' : '$';
                        if ($marketCap >= 1000000) {
                            $formatted = number_format($marketCap / 1000000, 2) . 'M';
                        } elseif ($marketCap >= 1000) {
                            $formatted = number_format($marketCap / 1000, 2) . 'K';
                        } else {
                            $formatted = number_format($marketCap, 2);
                        }
                    @endphp
                    {{ $currencySymbol }}&nbsp;{{ $formatted }}
                    <span class="ml-2 text-gray-500 text-base">{{ $currency }}</span>
                </span>
            </x-stat-card>
        @endif

        <!-- Current Price Card -->
        <x-stat-card aria-label="Current price" icon-bg="bg-green-700" label="Price">
            <x-slot:icon>
                <x-icon-pep-currency-sign class="w-5 h-5 text-white" />
            </x-slot:icon>
            <x-price-amount
                class="text-2xl"
                :value="$price"
                :currency-symbol="$currency === 'EUR' ? '€' : '$'"
                :currency-code="$currency"
                precision="8"
                dim-class="text-gray-700 dark:text-gray-500"
                dim-size-class="text-xl"
            />
        </x-stat-card>
    </div>
    <!-- Price Chart -->
    <div class="mb-8">
        <!-- Period Selector -->
        @php $currentPeriod = request('period', '24h'); @endphp
        <div class="flex items-center justify-end gap-2 mb-5" aria-label="Select time range">
            @php $periods = ['24h' => '24h', '7d' => '7D', '30d' => '30D']; @endphp
            @foreach($periods as $key => $label)
                <a
                    href="{{ route('price', ['period' => $key, 'currency' => request('currency', 'USD')]) }}"
                    class="flex items-center space-x-1 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 px-3 py-1 rounded-full transition-all duration-200 border border-green-200 hover:border-green-300 text-green-700 font-medium text-sm {{ $currentPeriod === $key ? 'ring-2 ring-green-600' : '' }}"
                    @if($currentPeriod === $key) aria-current="true" @endif
                >
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
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
