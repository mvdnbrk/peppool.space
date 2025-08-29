@props([
    'label' => '',
    'iconBg' => 'bg-gray-500',
    'ariaLabel' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow p-6']) }} @if($ariaLabel) aria-label="{{ $ariaLabel }}" role="status" @endif>
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 {{ $iconBg }} rounded-xl flex items-center justify-center">
                {{ $icon ?? '' }}
            </div>
        </div>
        <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
