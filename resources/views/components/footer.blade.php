@props(['network' => null])

<footer class="bg-white border-t mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div class="flex flex-col items-center md:items-start">
                <p class="font-medium text-green-700">real-time pepecoin blockchain explorer</p>
                <p class="text-sm text-gray-500 mt-1">© {{ date('Y') }} peppool.space, all rights reserved</p>
            </div>

            @if(isset($network))
            <div class="flex flex-col items-center md:items-end text-xs text-gray-500">
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
