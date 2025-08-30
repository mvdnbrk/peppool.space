@props(['latestBlocks' => []])

<div class="bg-white dark:bg-gray-800 rounded-b-lg shadow overflow-hidden">
    <div class="relative">
        <x-wave color="text-green-700" flip="true" class="-mt-px rotate-180 bg-gray-50 dark:bg-gray-900"/>
        <div class="px-6 py-2 bg-green-700">
            <h2 class="text-lg font-ubuntu font-bold italic text-white">Latest Blocks</h2>
        </div>
        <x-wave color="text-green-700" class="-mt-px"/>
    </div>
    <div class="overflow-hidden">
        @forelse($latestBlocks as $block)
            <a href="{{ route('block.show', $block['height']) }}" class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="px-3 py-1 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $block['height'] }}</span>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ substr($block['hash'], 0, 16) }}...{{ substr($block['hash'], -8) }}
                            </p>
                            <timestamp
                                x-data="timestamp"
                                datetime="{{ \Carbon\Carbon::createFromTimestamp($block['time'])->toAtomString() }}"
                                x-text="relativeTime"
                                class="text-sm text-gray-500 dark:text-gray-400"></timestamp>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $block['tx_count'] }} txs</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($block['size'] / 1024, 1) }} KB</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="px-6 py-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No blocks available</p>
            </div>
        @endforelse
    </div>
</div>
