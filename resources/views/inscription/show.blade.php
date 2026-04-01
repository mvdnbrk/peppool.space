<x-layout
    title="Inscription #{{ number_format($inscription->number) }} - peppool.space"
    :og_description="'Pepecoin Inscription #' . number_format($inscription->number) . ' — ' . $inscription->contentTypeForHumans() . ' on peppool.space'"
    og_image="pepecoin-inscription.png"
>
        <div class="mb-6 md:mb-8">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                Inscription #{{ number_format($inscription->number) }}
            </h1>
            @if($inscription->hasTitle())
                <p class="mt-1 text-base md:text-lg text-gray-500 dark:text-gray-400">
                    {{ $inscription->getTitle() }}
                </p>
            @endif
        </div>

        @php
            $createdAt = \Carbon\Carbon::createFromTimestamp($inscription->timestamp);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
            {{-- Details (left on desktop, below on mobile) --}}
            <div class="space-y-6 order-2 lg:order-1">
                @if($inscription->hasParents())
                    <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ Str::plural('Parent Inscription', $inscription->getParents()->count()) }}</h2>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($inscription->getParents() as $parentId)
                                <li class="px-6 py-3 font-mono text-sm flex items-center gap-1 min-w-0" title="{{ $parentId }}">
                                    <a href="{{ route('inscription.show', $parentId) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex min-w-0">
                                        <x-truncate-middle :value="$parentId" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                        <x-description-item label="Inscription ID" :mono="true" class="flex items-center gap-1 min-w-0" title="{{ $inscriptionId }}">
                            <x-truncate-middle :value="$inscriptionId" />
                            <x-copy-to-clipboard :value="$inscriptionId" />
                        </x-description-item>

                        @if($inscription->isDelegate())
                            <x-description-item label="Delegate" :mono="true" class="flex items-center gap-1 min-w-0" title="{{ $inscription->delegate }}">
                                <a href="{{ route('inscription.show', $inscription->delegate) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex min-w-0">
                                    <x-truncate-middle :value="$inscription->delegate" />
                                </a>
                            </x-description-item>
                        @endif

                        <x-description-item label="Owner" :mono="true">
                            @if($inscription->address)
                                <a href="{{ route('address.show', $inscription->address) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $inscription->address }}
                                </a>
                            @endif
                        </x-description-item>

                        <x-description-item label="Content Type">
                            {{ $inscription->contentTypeForHumans() }}
                            <span class="text-gray-400 dark:text-gray-500">({{ $inscription->effective_content_type }})</span>
                        </x-description-item>

                        <x-description-item label="Content Size" :mono="true">
                            @php
                                $contentLength = $inscription->content_length ?? 0;
                            @endphp
                            @if($contentLength < 1024)
                                {{ Number::format($contentLength) }} bytes
                            @else
                                {{ Number::fileSize($contentLength) }}
                                <span class="text-gray-400 dark:text-gray-500">({{ Number::format($contentLength) }} bytes)</span>
                            @endif
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

                @if($inscription->hasChildren())
                    <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ number_format($inscription->getChildCount()) }} {{ Str::plural('Child Inscription', $inscription->getChildCount()) }}</h2>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($inscription->getChildren() as $childId)
                                <li class="px-6 py-3 font-mono text-sm flex items-center gap-1 min-w-0" title="{{ $childId }}">
                                    <a href="{{ route('inscription.show', $childId) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex min-w-0">
                                        <x-truncate-middle :value="$childId" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(count($references) > 0)
                    <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ count($references) }} Recursive {{ Str::plural('Module', count($references)) }}</h2>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($references as $referenceId)
                                <li class="px-6 py-3 font-mono text-sm flex items-center gap-1 min-w-0" title="{{ $referenceId }}">
                                    <a href="{{ route('inscription.show', $referenceId) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex min-w-0">
                                        <x-truncate-middle :value="$referenceId" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($inscription->hasTraits())
                    <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Traits</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($inscription->getTraits() as $type => $value)
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center border border-gray-100 dark:border-gray-700">
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ $type }}</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $value }}">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Content Preview (right on desktop, top on mobile) --}}
            <div class="order-1 lg:order-2">
                <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <x-inscription-preview :inscription="$inscription" :contentUrl="$contentUrl" />
                </div>
            </div>
        </div>
</x-layout>
