@props([
    'value',
    'offset' => 6,
])

@php
    $prefix = Str::substr($value, 0, -$offset);
    $suffix = Str::substr($value, -$offset);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex max-w-full min-w-0']) }}>
    <span class="flex min-w-0">
        <span class="overflow-hidden text-ellipsis whitespace-nowrap min-w-0">{{ $prefix }}</span>
    </span>
    <span class="whitespace-nowrap">{{ $suffix }}</span>
</span>
