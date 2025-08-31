<x-layout title="Pepecoin Price - peppool.space" og_image="pepecoin-price.png">
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
