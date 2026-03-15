<x-layout
    title="Inscription #{{ isset($inscription) ? number_format($inscription->number) : 'Not Found' }} - peppool.space"
    :og_description="(isset($inscription) ? 'Pepeinal #' . number_format($inscription->number) . ' — ' . $inscription->contentType : 'Inscription not found') . ' on peppool.space'"
    og_image="pepecoin-inscription.png"
>
        @if(isset($error))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon-exclamation-circle class="w-5 h-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">{{ $error }}</h3>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-6 md:mb-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                    Inscription #{{ number_format($inscription->number) }}
                </h1>
            </div>

            @php
                $createdAt = \Carbon\Carbon::createFromTimestamp($inscription->timestamp);
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                {{-- Details (left on desktop, below on mobile) --}}
                <div class="space-y-6 order-2 lg:order-1">
                    <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <x-description-item label="Inscription ID" :mono="true" class="flex items-center gap-1" title="{{ $inscriptionId }}">
                                <x-truncate-middle :value="$inscriptionId" />
                                <x-copy-to-clipboard :value="$inscriptionId" />
                            </x-description-item>

                            <x-description-item label="Owner" :mono="true">
                                <a href="{{ route('address.show', $inscription->address) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $inscription->address }}
                                </a>
                            </x-description-item>

                            <x-description-item label="Content Type">
                                {{ $inscription->contentTypeForHumans() }}
                                <span class="text-gray-400 dark:text-gray-500">({{ $inscription->contentType }})</span>
                            </x-description-item>

                            <x-description-item label="Content Size" :mono="true">
                                {{ Number::fileSize($inscription->contentLength) }}
                                <span class="text-gray-400 dark:text-gray-500">({{ Number::format($inscription->contentLength) }} bytes)</span>
                            </x-description-item>

                            <x-description-item label="Creation Date">
                                {{ $createdAt->format('M j, Y \a\t g:i A') }}
                                <span class="text-gray-400 dark:text-gray-500">({{ $createdAt->diffForHumans(parts: 1, options: \Carbon\CarbonInterface::ROUND) }})</span>
                            </x-description-item>

                            <x-description-item label="Creation Block" :mono="true">
                                <a href="{{ route('block.show', $inscription->height) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ number_format($inscription->height) }}
                                </a>
                            </x-description-item>
                        </dl>
                    </div>
                </div>

                {{-- Content Preview (right on desktop, top on mobile) --}}
                <div class="order-1 lg:order-2">
                    <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <x-inscription-preview :inscription="$inscription" :contentUrl="$contentUrl" />
                    </div>
                </div>
            </div>
        @endif
</x-layout>
