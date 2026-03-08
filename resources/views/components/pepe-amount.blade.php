@props([
    'amount' => 0,
    'decimals' => 6,
])

@php
    $full = format_pepe($amount);
    $formatted = format_pepe($amount, $decimals);
    $parts = explode('.', $formatted);
    $whole = $parts[0];
    $decimal = isset($parts[1]) ? '.' . $parts[1] : '';
@endphp

<span title="{{ $full }}" {{ $attributes->merge(['class' => 'inline-flex flex-row flex-nowrap items-baseline truncate max-w-full']) }}>
    <span class="shrink-0">{{ $whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 font-normal truncate shrink">{{ $decimal }}</span>
</span>
