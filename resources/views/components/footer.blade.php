@props(['network' => null])

<footer class="bg-green-700 mt-16 relative text-white border-b-12 border-green-900">
    <div class="absolute -top-16 left-0 right-0 w-full h-16 overflow-hidden">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" class="w-full h-full transform scale-x-[-1] rotate-180">
            <path fill="#15803d" d="M0,224L48,224C96,224,192,224,288,197.3C384,171,480,117,576,117.3C672,117,768,171,864,202.7C960,235,1056,245,1152,234.7C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2 pb-12">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
            <div class="flex flex-col items-center md:items-start">
                <p class="font-medium text-white">real-time pepecoin blockchain explorer</p>
                <p class="text-sm text-green-100 mt-1">© {{ date('Y') }} peppool.space, all rights reserved</p>
            </div>

            @if(isset($network))
            <div class="flex flex-col items-center md:items-end text-xs text-green-100">
                <span>{{ $network['subversion'] ?? 'Unknown' }}</span>
                <div class="mt-2 flex items-center space-x-2">
                    <div class="w-1.5 h-1.5 bg-green-400 rounded-full"></div>
                    <span>{{ $network['connections'] ?? 0 }} peers</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</footer>
