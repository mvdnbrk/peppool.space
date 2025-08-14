<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-4 sm:py-6">
            <div class="flex justify-between items-center">
                <!-- Logo and Site Title -->
                <a href="{{ route('homepage') }}" class="flex items-center">
                    <x-icon-logo class="w-12 h-12"/>
                    <h1 class="ml-3 sm:ml-4 text-3xl sm:text-4xl italic tracking-wide text-black">
                        <span class="text-green-700 font-bold">Pep</span>pool<span class="text-lg sm:text-xl">.space</span>
                    </h1>
                </a>

                <!-- PEPE Price -->
                @if(Cache::has('pepecoin_price_usd'))
                    <div class="flex items-center space-x-1 sm:space-x-2 bg-green-50 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full">
                        <span class="text-green-700 font-medium text-xs sm:text-sm">PEPE</span>
                        <span class="text-gray-700 font-semibold text-xs sm:text-sm">
                            ${{ number_format(Cache::get('pepecoin_price_usd'), 8, '.', ',') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
