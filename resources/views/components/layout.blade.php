@props([
    'title' => 'Peppool Explorer',
    'network' => null,
    'og_image' => 'default-card-large.png',
    'og_description' => 'Real-time Pepecoin blockchain explorer. View blocks, transactions, addresses, and more on the Pepecoin network.',
])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <meta name="description" content="{{ $og_description }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $og_description }}">
    <meta property="og:image" content="https://cdn.peppool.space/opengraph/{{ $og_image }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title }}">
    <meta property="twitter:description" content="{{ $og_description }}">
    <meta property="twitter:image" content="https://cdn.peppool.space/opengraph/{{ $og_image }}">

    <x-favicons />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(config('services.fathom.site_id'))
    <script src="https://cdn.usefathom.com/script.js" data-site="{{ config('services.fathom.site_id') }}" defer></script>
    @endif
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <x-header />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <x-footer :network="$network" />

    <!-- Floating Theme Toggle -->
    <x-theme-toggle />

    <!-- Development Notice Widget -->
    <x-development-notice />
</body>
</html>
