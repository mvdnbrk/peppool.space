<x-layout title="Active Nodes - peppool.space" og_description="Active Pepecoin (PEP) nodes globally.">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">Active Nodes</h1>
        <p class="text-sm md:text-base text-gray-500">Live view of Pepecoin network peers and their geographic distribution.</p>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Total Peers" iconBg="bg-green-500">
            <x-slot name="icon"><x-icon-computer-desktop class="w-6 h-6 text-white" /></x-slot>
            {{ number_format($stats['total']) }}
        </x-stat-card>

        <x-stat-card label="Online Nodes" iconBg="bg-blue-500">
            <x-slot name="icon"><x-icon-check-circle class="w-6 h-6 text-white" /></x-slot>
            {{ number_format($stats['online']) }}
        </x-stat-card>

        <x-stat-card label="Top Country" iconBg="bg-purple-500">
            <x-slot name="icon"><x-icon-database class="w-6 h-6 text-white" /></x-slot>
            {{ $stats['countries']->keys()->first() ?? 'Unknown' }}
        </x-stat-card>

        <x-stat-card label="Protocol Version" iconBg="bg-orange-500">
            <x-slot name="icon"><x-icon-terminal class="w-6 h-6 text-white" /></x-slot>
            {{ $nodes->first()?->version ?? 'N/A' }}
        </x-stat-card>
    </div>

    <!-- Top Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Top Countries -->
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100 border-b pb-2">Top Countries</h2>
            <div class="space-y-4">
                @foreach($stats['countries'] as $country => $count)
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
                            @foreach($stats['subversions'] as $subversion => $count)
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
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Recent Peers</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3">IP Address</th>
                        <th class="px-6 py-3">Subversion</th>
                        <th class="px-6 py-3">ISP</th>
                        <th class="px-6 py-3 text-right">Last Seen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($nodes as $node)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($node->country_code)
                                <span class="mr-2 text-lg">{{ flag($node->country_code) }}</span>
                                @endif
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $node->city ?? 'Unknown' }}, {{ $node->country_code }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                            {{ $node->ip }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            <span @if(str_contains($node->subversion, 'peppool.space')) class="text-green-600 font-bold" @endif>
                                {{ $node->subversion }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $node->isp ?? 'Unknown' }}
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
