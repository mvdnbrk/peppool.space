<x-layout
    title="{{ $pool->name }} Mining Pool - peppool.space"
    :network="$network"
    og_description="Detailed statistics for {{ $pool->name }} mining pool on the Pepecoin network. View pool share, hashrate history, and recently mined blocks.">

    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pool->name }}</h1>
                @if($pool->link)
                    <a href="{{ $pool->link }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-green-700 dark:hover:text-green-400 transition-colors">
                        <x-icon-share class="w-5 h-5" />
                    </a>
                @endif
            </div>
            <p class="text-gray-600 dark:text-gray-400">Mining pool performance and block history.</p>
        </div>
        <div class="flex flex-wrap gap-4">
            <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="text-xs text-gray-500 uppercase font-semibold">Current Share</span>
                <div class="text-lg font-bold text-gray-900 dark:text-white" id="pool-share-display">--%</div>
            </div>
            <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="text-xs text-gray-500 uppercase font-semibold">Hashrate</span>
                <div class="text-lg font-bold text-gray-900 dark:text-white" id="pool-hashrate-display">-- GH/s</div>
            </div>
        </div>
    </div>

    <!-- Hashrate History Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-6">
            <div 
                data-vue="pool-hashrate-chart"
                data-props='@json(["apiUrl" => route("api.mining.pool", ["slug" => $pool->slug])])'
            >
                <div class="animate-pulse text-gray-400">Loading chart...</div>
            </div>
        </div>
    </div>

    <!-- Recent Blocks -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Blocks</h2>
            <span class="px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">
                {{ $pool->blocks()->count() }} Total
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Height</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Hash</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($pool->blocks()->orderByDesc('height')->paginate(25) as $block)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('block.show', $block->height) }}" class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900/50 rounded-md group hover:bg-green-200 dark:hover:bg-green-800/50 transition-colors">
                                <span class="text-sm font-bold text-green-700 dark:text-green-400">
                                    {{ $block->height }}
                                </span>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('block.show', $block->hash) }}" class="text-gray-500 dark:text-gray-400 font-mono text-sm hover:text-green-700 dark:hover:text-green-400 transition-colors">
                                {{ substr($block->hash, 0, 16) }}...{{ substr($block->hash, -8) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $block->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $block->tx_count }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $pool->blocks()->orderByDesc('height')->paginate(25)->links() }}
        </div>
    </div>
</x-layout>
