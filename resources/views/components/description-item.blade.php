@props([
    'label' => null,
    'value' => null,
    'mono' => false,
])

@if($value !== '' && $value !== null || $slot->isNotEmpty())
    <div class="px-6 py-3">
        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</dt>
        <dd {{ $attributes->class([
            'text-sm text-gray-900 dark:text-white mt-0.5',
            'font-mono' => $mono,
        ]) }}>
            {{ $value ?? $slot }}
        </dd>
    </div>
@endif
