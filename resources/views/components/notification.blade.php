@props(['type' => 'info', 'icon' => null])

@php
$classes = match($type) {
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    'coming-soon' => 'bg-gradient-to-r from-yellow-50 to-orange-50 border-yellow-300 text-yellow-900',
    default => 'bg-blue-50 border-blue-200 text-blue-800'
};

$iconClasses = match($type) {
    'warning' => 'text-yellow-400',
    'info' => 'text-blue-400',
    'success' => 'text-green-400',
    'error' => 'text-red-400',
    'coming-soon' => 'text-yellow-500',
    default => 'text-blue-400'
};

$defaultIcon = match($type) {
    'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
    'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    'coming-soon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
};
@endphp

<div {{ $attributes->merge(['class' => "border rounded-lg p-4 $classes"]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon ?? $defaultIcon }}" />
            </svg>
        </div>
        <div class="ml-3 flex-1">
            {{ $slot }}
        </div>
    </div>
</div>
