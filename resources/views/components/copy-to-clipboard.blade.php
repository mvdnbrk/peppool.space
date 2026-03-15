@props([
    'value' => ''
])

@php
    $id = 'copy-' . Str::random(8);
@endphp

<span class="inline-flex">
    <el-copyable id="{{ $id }}" class="hidden">{{ $value }}</el-copyable>
    <button
        type="button"
        command="--copy"
        commandfor="{{ $id }}"
        title="Copy to clipboard"
        class="group inline-flex items-center justify-center w-8 h-8 rounded-md text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 cursor-pointer shrink-0"
    >
        <x-icon-copy class="w-5 h-5 group-data-[copied]:hidden" />
        <x-icon-check class="w-5 h-5 hidden group-data-[copied]:block" />
    </button>
</span>
