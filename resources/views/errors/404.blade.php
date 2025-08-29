@php
$title = config('app.name') . ' | Page not found';
@endphp

<x-layout :title="$title" og_image="peppool-error-404.png">
    <main class="flex-grow flex items-center justify-center px-4 py-12 sm:py-24">
        <div class="text-center">
            <p class="text-base font-semibold text-green-700">404</p>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight text-balance text-gray-900 dark:text-white sm:text-4xl">
                Page not found
            </h1>
            <p class="mt-6 text-lg font-medium text-pretty text-gray-500 dark:text-gray-400 sm:text-xl">
                Sorry, we couldn’t find the page you’re looking for.
            </p>
        </div>
    </main>
</x-layout>
