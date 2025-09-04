<div {{ $attributes->merge(['class' => '']) }}>
    <form action="{{ route('search.store') }}" method="POST" class="relative">
        @csrf
        <label for="global-search" class="sr-only">Search</label>
        <div class="flex rounded-lg shadow-sm ring-1 ring-gray-300 dark:ring-gray-700 focus-within:ring-2 focus-within:ring-green-600 bg-white dark:bg-gray-900 overflow-hidden">
            <input
                type="text"
                id="global-search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search block height/hash, transaction ID, or address"
                aria-label="Search block height/hash, transaction ID, or address"
                class="flex-1 px-4 py-3 border-0 bg-transparent text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 text-sm focus:outline-none focus:ring-0"
                autocomplete="off"
                required
            />
            <button type="submit" aria-label="Search" class="px-4 bg-green-700 text-white text-sm font-medium hover:bg-green-800 focus:outline-none inline-flex items-center justify-center cursor-pointer">
                <x-icon-search class="w-5 h-5" />
            </button>
        </div>
    </form>

    @if (session('error'))
        <div class="mt-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm rounded-md p-3">
            {{ session('error') }}
        </div>
    @endif
</div>
