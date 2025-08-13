@props(['breadcrumb' => null])

<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-4 sm:py-6">
            <!-- Mobile Layout -->
            <div class="flex flex-col space-y-3 sm:hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center">
                            <x-icon-logo class="w-8 h-8"/>
                            <h1 class="ml-2 text-lg font-bold text-green-700">peppool.space</h1>
                        </a>
                        @if($breadcrumb)
                            @if(is_array($breadcrumb))
                                @foreach($breadcrumb as $item)
                                    <span class="text-gray-400 mx-2">/</span>
                                    @if(isset($item['url']))
                                        <a href="{{ $item['url'] }}" class="text-blue-600 hover:text-blue-800 text-sm">{{ $item['label'] }}</a>
                                    @else
                                        <span class="text-gray-600 text-sm">{{ $item['label'] }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-gray-400 mx-2">/</span>
                                <span class="text-gray-600 text-sm">{{ $breadcrumb }}</span>
                            @endif
                        @endif
                    </div>
                    <div class="flex items-center">
                        @if(Cache::has('pepecoin_price_usd'))
                            <div class="flex flex-col items-end">
                                <div class="flex items-center space-x-1 bg-green-50 px-2 py-1 rounded-full">
                                    <span class="text-green-700 font-medium text-xs">PEPE</span>
                                    <span class="text-gray-700 font-semibold text-xs">${{ number_format(Cache::get('pepecoin_price_usd'), 8, '.', ',') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden sm:flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="/" class="flex items-center">
                        <x-icon-logo class="w-12 h-12"/>
                        <h1 class="ml-3 text-2xl font-bold text-green-700">peppool.space</h1>
                    </a>
                    @if($breadcrumb)
                        @if(is_array($breadcrumb))
                            @foreach($breadcrumb as $item)
                                <span class="text-gray-400">/</span>
                                @if(isset($item['url']))
                                    <a href="{{ $item['url'] }}" class="text-blue-600 hover:text-blue-800">{{ $item['label'] }}</a>
                                @else
                                    <span class="text-gray-600">{{ $item['label'] }}</span>
                                @endif
                            @endforeach
                        @else
                            <span class="text-gray-400">/</span>
                            <span class="text-gray-600">{{ $breadcrumb }}</span>
                        @endif
                    @endif
                </div>
                <div class="flex items-center">
                    @if(Cache::has('pepe_price'))
                        <div class="flex flex-col items-end">
                            <div class="flex items-center space-x-1 sm:space-x-2 bg-green-50 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full">
                                <span class="text-green-700 font-medium text-sm sm:text-base">PEPE</span>
                                <span class="text-gray-700 font-semibold text-sm sm:text-base">${{ number_format(Cache::get('pepe_price'), 8, '.', ',') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
