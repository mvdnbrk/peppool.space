<x-layout :title="$title" :og_image="$og_image" :og_description="$description">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">PEPE Currency Converter</h1>
        <p class="text-sm md:text-base text-gray-500">Convert Pepecoin (PEPE) to USD, EUR and other currencies with real-time rates.</p>
    </div>

    <!-- Main Converter Card -->
    <div 
        data-vue="currency-converter"
        data-props='{"usdRate": {{ $price->usd ?? 0 }}, "eurRate": {{ $price->eur ?? 0 }}}'
    ></div>

</x-layout>
