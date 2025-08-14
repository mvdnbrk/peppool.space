<header class="bg-white relative">
    <div class="w-full h-6 overflow-hidden -mt-px text-green-700">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" class="w-full h-full">
            <path fill="currentColor" d="M0,224L48,224C96,224,192,224,288,197.3C384,171,480,117,576,117.3C672,117,768,171,864,202.7C960,235,1056,245,1152,234.7C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="py-2 sm:py-4">
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
    <div class="bg-gray-50 w-full h-6 overflow-hidden -mt-px text-white">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" class="w-full h-full transform scale-x-[-1]">
            <path fill="currentColor" d="M0,224L48,224C96,224,192,224,288,197.3C384,171,480,117,576,117.3C672,117,768,171,864,202.7C960,235,1056,245,1152,234.7C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>
</header>
