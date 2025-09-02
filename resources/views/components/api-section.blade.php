{{-- Class-based component: variables provided by App\View\Components\ApiSection --}}

<div id="{{ $sectionId }}" class="border border-gray-200 rounded-lg p-4 md:p-6 bg-white">
    <div class="flex flex-wrap items-center mb-3 md:mb-4 gap-2">
        <span class="{{ $badgeClasses }} text-xs font-medium px-2.5 py-0.5 rounded">
            {{ $method }}
        </span>
        <code class="text-base md:text-lg font-mono break-all">
            {{ $path }}
        </code>
    </div>

    @if($description)
        <p class="text-gray-600 mb-4">
            {!! $description !!}
        </p>
    @endif

    @isset($example)
        <div class="mb-4">
            <h4 class="font-semibold mb-2">Example</h4>
            <div class="bg-gray-900 text-white p-2 md:p-3 rounded text-xs md:text-sm overflow-x-auto">
                {{ $example }}
            </div>
        </div>
    @endisset

    @isset($response)
        <div class="mb-4">
            <h4 class="font-semibold mb-2">Response</h4>
            <div class="bg-gray-50 p-2 md:p-3 rounded border border-gray-200 overflow-x-auto">
                @if($responseContentType)
                    <p class="text-xs md:text-sm text-gray-600 mb-1">Content-Type: {{ $responseContentType }}</p>
                @endif
                {{ $response }}
            </div>
        </div>
    @endisset

    @isset($fields)
        <div class="mb-2">
            <h4 class="font-semibold mb-2">Response Fields</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs md:text-sm">
                    <thead>
                        <tr class="border-b border-gray-300">
                            <th class="text-left py-2 font-semibold">Field</th>
                            <th class="text-left py-2 font-semibold">Type</th>
                            <th class="text-left py-2 font-semibold">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{ $fields }}
                    </tbody>
                </table>
            </div>
        </div>
    @endisset
</div>
