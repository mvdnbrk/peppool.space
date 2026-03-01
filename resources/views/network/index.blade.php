<x-layout title="Network Status - peppool.space" og_description="Real-time overview of the Pepecoin network through our distributed infrastructure.">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">Network Status</h1>
        <p class="text-sm md:text-base text-gray-500">Real-time overview of the Pepecoin network through our distributed infrastructure.</p>
    </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <x-stat-card label="Network Discovery" iconBg="bg-blue-500">
                    <x-slot name="icon"><x-icon-nodes class="w-6 h-6 text-white" /></x-slot>
                    {{ number_format($stats->get('total')) }}
                </x-stat-card>
        
                <x-stat-card label="Live Connections" iconBg="bg-green-500">
                    <x-slot name="icon"><x-icon-check-circle class="w-6 h-6 text-white" /></x-slot>
                    {{ number_format($stats->get('online')) }}
                </x-stat-card>
                    <x-stat-card label="Top Country" iconBg="bg-purple-500">
                <x-slot name="icon"><x-icon-globe class="w-6 h-6 text-white" /></x-slot>
                {{ $stats->get('countries')->keys()->first() ?? 'Unknown' }}
            </x-stat-card>
    
            <x-stat-card label="Protocol Version" iconBg="bg-orange-500">
                <x-slot name="icon"><x-icon-terminal class="w-6 h-6 text-white" /></x-slot>
                {{ $stats->get('version') }}
            </x-stat-card>
        </div>
    
        <!-- Top Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Top Countries -->
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Top Countries</h2>
                <div class="space-y-4">
                    @foreach($stats->get('countries') as $country => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $country ?? 'Unknown' }}</span>
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-green-900/30 dark:text-green-500">
                            {{ $count }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
    
            <!-- Top Subversions -->
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Top Client Versions</h2>
                <div class="space-y-4">
                    @foreach($stats->get('subversions') as $subversion => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[250px]" title="{{ $subversion }}">{{ $subversion }}</span>
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-green-900/30 dark:text-green-500">
                            {{ $count }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    
            <!-- Node List (Full Width) -->
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Live Connections</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="px-6 py-3 w-10"></th>
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3 hidden sm:table-cell">Client</th>
                        <th class="px-6 py-3 hidden sm:table-cell">ISP</th>
                        <th class="px-6 py-3 hidden sm:table-cell">Connection</th>
                        <th class="px-6 py-3 text-right">Last Seen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($nodes as $node)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4">
                            @if($node->country_code)
                            <span class="text-xl" title="{{ $node->country }}">{{ flag($node->country_code) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $node->country ?? 'Unknown' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $node->city ?? 'Unknown' }}{{ $node->region ? ', ' . $node->region : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $node->client_version }}
                            </div>
                            @if($node->user_comment)
                            <div class="text-xs text-gray-500 dark:text-gray-400 @if(str_contains($node->user_comment, 'peppool.space')) text-green-600 dark:text-green-500 @endif" title="{{ $node->user_comment }}">
                                {{ Str::limit($node->user_comment, 25) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                            {{ $node->isp ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @foreach($node->sources ?? [] as $source)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                    {{ $source }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right whitespace-nowrap">
                            {{ $node->last_seen_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
