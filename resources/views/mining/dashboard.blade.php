<x-layout
    title="Mining Dashboard - peppool.space"
    :network="$network"
    og_description="Pepecoin Mining Dashboard: View pool hashrate distribution, network difficulty, and historical hashrate stats.">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mining Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Real-time statistics on Pepecoin mining pools and network hashrate.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Pool Distribution Pie Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mining Pool Share</h2>
                <div class="flex items-center space-x-2">
                    <!-- Future: Daily/Weekly Toggle -->
                </div>
            </div>
            <div class="p-6">
                <div 
                    data-vue="mining-pool-share"
                    data-props='@json(["apiUrl" => route("api.mining.pools")])'
                    class="min-h-[300px] flex items-center justify-center"
                >
                    <div class="animate-pulse text-gray-400">Loading charts...</div>
                </div>
            </div>
        </div>

        <!-- Hashrate History Line Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Network Hashrate History</h2>
            </div>
            <div class="p-6">
                <div 
                    data-vue="mining-hashrate-history"
                    data-props='@json(["apiUrl" => route("api.mining.hashrate")])'
                    class="min-h-[300px] flex items-center justify-center"
                >
                    <div class="animate-pulse text-gray-400">Loading charts...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pools Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Identified Pools</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Pool Name</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Blocks</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Payout Addresses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach(App\Models\Pool::where('name', '!=', 'Unknown')->whereHas('blocks')->withCount('blocks')->orderByDesc('blocks_count')->get() as $pool)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            <a href="{{ route('mining.pool', $pool->slug) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $pool->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ number_format($pool->blocks_count) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400">
                            <a href="{{ $pool->link }}" target="_blank" rel="noopener" class="hover:underline">{{ $pool->link }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                            @foreach(array_slice($pool->addresses, 0, 1) as $address)
                                <code class="block">{{ $address }}</code>
                            @endforeach
                            @if(count($pool->addresses) > 1)
                                <span class="text-xs text-gray-400">+ {{ count($pool->addresses) - 1 }} more</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
