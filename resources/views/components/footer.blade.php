@props(['network' => null])

<footer class="bg-green-700 mt-16 relative text-white border-b-12 border-green-900">
    <div class="absolute -top-16 left-0 right-0 w-full h-16 overflow-hidden">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" class="w-full h-full transform scale-x-[-1] rotate-180">
            <path fill="#15803d" d="M0,224L48,224C96,224,192,224,288,197.3C384,171,480,117,576,117.3C672,117,768,171,864,202.7C960,235,1056,245,1152,234.7C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2 pb-12">
        <!-- Footer Navigation (top) -->
        <nav class="pb-6 mb-6 border-b border-green-600/50" aria-label="Footer navigation">
            <ul class="flex flex-wrap items-center justify-center md:justify-start gap-x-8 gap-y-4">
                <li>
                    <a href="{{ route('about') }}" class="text-sm font-medium text-green-100 hover:text-white transition-colors">
                        About PEPE
                    </a>
                </li>
                <li>
                    <a href="{{ route('price') }}" class="text-sm font-medium text-green-100 hover:text-white transition-colors">
                        Price Chart
                    </a>
                </li>
                <li>
                    <a href="{{ route('converter') }}" class="text-sm font-medium text-green-100 hover:text-white transition-colors">
                        Converter
                    </a>
                </li>
                <li>
                    <a href="{{ route('docs.api') }}" class="text-sm font-medium text-green-100 hover:text-white transition-colors">
                        API
                    </a>
                </li>
                <li>
                    <a href="{{ route('search.index') }}" class="text-sm font-medium text-green-100 hover:text-white transition-colors">
                        Search
                    </a>
                </li>
                <li>
                    <a href="{{ route('sponsor') }}" class="text-sm font-medium text-green-100 hover:text-white transition-colors">
                        Sponsor
                    </a>
                </li>
            </ul>
        </nav>

        <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
            <div class="flex flex-col items-center md:items-start">
                <p class="text-lg font-medium text-white">real-time pepecoin blockchain explorer</p>
                @if(isset($network))
                <div class="mt-1 flex items-center gap-2 text-sm text-green-100">
                    @if(!empty($network['subversion']))
                        <span>{{ $network['subversion'] }}</span>
                    @endif
                    <div class="w-1.5 h-1.5 bg-green-400 rounded-full"></div>
                    <span>{{ $network['connectionCount'] ?? 0 }} peers</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Bottom row: Copyright (left) and Hosting & Analytics (right) -->
        <div class="mt-1 md:mt-4 flex flex-col md:flex-row items-center md:items-center justify-between gap-4">
            <p class="mt-4 md:mt-0 text-sm text-green-100">© {{ date('Y') }} peppool.space, all rights reserved</p>

            <div class="mt-5 md:mt-0 flex flex-col md:flex-row items-center gap-6">
                <div class="flex flex-col items-center md:flex-row md:items-center md:space-x-2 space-y-1 md:space-y-0 text-xs text-green-100 leading-5">
                    <p>Proudly hosted with</p>
                    <a
                        href="https://m.do.co/c/7a24c68b1e6d"
                        class="hover:text-white transition-colors"
                        target="_blank"
                        rel="noopener"
                    >
                        <span class="sr-only">Digital Ocean</span>
                        <x-icon-digitalocean class="h-6" />
                    </a>
                </div>
                <div class="flex flex-col items-center md:flex-row md:items-center md:space-x-2 space-y-1 md:space-y-0 text-xs text-green-100 leading-5">
                    <p>privacy-first insights with</p>
                    <a
                        href="https://usefathom.com/ref/FI15PB"
                        class="hover:text-white transition-colors"
                        target="_blank"
                        rel="noopener"
                    >
                        <span class="sr-only">Fathom Analytics</span>
                        <x-icon-fathom class="h-6" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
