@props([
    'amount' => 0,
])

@php
    $formatted = format_pepe($amount);
    $parts = explode('.', $formatted);
    $whole = $parts[0];
    $decimal = isset($parts[1]) ? '.' . $parts[1] : '';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex flex-row flex-nowrap items-baseline truncate max-w-full']) }}>
    <span class="shrink-0">{{ $whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 truncate shrink">{{ $decimal }}</span>
</span>
