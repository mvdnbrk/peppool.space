<div {{ $attributes->merge(['class' => 'inline-flex items-baseline']) }}>
    <span class="inline-flex space-x-0">
        <span>{!! $currencySymbol !!}</span>
        <span>
            @if($hasSignificantDecimals)
                {{ $intPart }}.<span class="{{ $dimClass }} {{ $dimSizeClass }} tracking-wider px-px">{{ $leadingZeros }}</span><span>{{ $significantDecimals }}</span>
            @else
                {{ $intPart }}
            @endif
        </span>
    </span>
    @if($currencyCode)
        <span class="ml-2 text-gray-500 text-base">{{ $currencyCode }}</span>
    @endif
</div>
