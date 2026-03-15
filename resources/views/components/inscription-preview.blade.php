@props([
    'inscription',
    'contentUrl' => null,
])

@php
    $contentUrl = $contentUrl ?? '/content/' . $inscription->id;
    $type = $inscription->contentType;
@endphp

<div {{ $attributes->merge(['class' => 'aspect-square flex items-center justify-center bg-gray-50 dark:bg-gray-800']) }}>
    @if(str_starts_with($type, 'image/'))
        <img
            src="{{ $contentUrl }}"
            alt="Inscription #{{ number_format($inscription->number) }}"
            class="w-full h-full object-cover [image-rendering:pixelated]"
            loading="lazy"
        />
    @elseif(str_starts_with($type, 'text/html'))
        <iframe
            src="{{ $contentUrl }}"
            class="w-full h-full border-0"
            sandbox="allow-scripts"
            loading="lazy"
        ></iframe>
    @elseif(str_starts_with($type, 'text/'))
        <iframe
            src="{{ $contentUrl }}"
            class="w-full h-full border-0"
            loading="lazy"
        ></iframe>
    @elseif(str_starts_with($type, 'audio/'))
        <audio controls src="{{ $contentUrl }}" class="w-3/4"></audio>
    @elseif(str_starts_with($type, 'video/'))
        <video controls src="{{ $contentUrl }}" class="max-w-full max-h-full"></video>
    @else
        <a href="{{ $contentUrl }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
            View content ({{ $inscription->contentTypeForHumans() }})
        </a>
    @endif
</div>
