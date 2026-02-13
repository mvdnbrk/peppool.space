@props([
    'amount' => 0,
])

@php
    $formatted = format_pepe($amount);
    $parts = explode('.', $formatted);
    $whole = $parts[0];
    $decimal = isset($parts[1]) ? '.' . $parts[1] : '';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-baseline']) }}>
    <span>{{ $whole }}</span><span class="text-[0.85em] opacity-80">{{ $decimal }}</span>
</span>
