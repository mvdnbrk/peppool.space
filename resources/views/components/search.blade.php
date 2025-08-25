<div {{ $attributes->merge(['class' => '']) }}>
    <form action="{{ route('search.store') }}" method="POST" class="relative">
        @csrf
        <label for="global-search" class="sr-only">Search</label>
        <div class="flex rounded-lg shadow-sm ring-1 ring-gray-300 focus-within:ring-2 focus-within:ring-green-600 bg-white overflow-hidden">
            <input
                type="text"
                id="global-search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search block height/hash, transaction ID, or address"
                aria-label="Search block height/hash, transaction ID, or address"
                class="flex-1 px-4 py-3 border-0 bg-transparent placeholder-gray-400 text-sm focus:outline-none focus:ring-0"
                autocomplete="off"
                required
            />
            <button type="submit" aria-label="Search" class="px-4 bg-green-700 text-white text-sm font-medium hover:bg-green-800 focus:outline-none inline-flex items-center justify-center cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.1-4.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>
    </form>

    @if (session('error'))
        <div class="mt-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-md p-3">
            {{ session('error') }}
        </div>
    @endif
</div>
