@props(['title' => 'Peppool Explorer', 'network' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(config('services.fathom.site_id'))
    <script src="https://cdn.usefathom.com/script.js" data-site="{{ config('services.fathom.site_id') }}" defer></script>
    @endif
</head>
<body class="bg-gray-50 min-h-screen">
    <x-header />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <x-footer :network="$network" />
</body>
</html>
