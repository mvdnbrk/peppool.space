<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>peppool.space</title>

        @vite('resources/css/app.css')

        @if(config('services.fathom.site_id'))
        <script src="https://cdn.usefathom.com/script.js" data-site="{{ config('services.fathom.site_id') }}" defer></script>
        @endif
    </head>
    <body class="p-8">

        <div class="flex items-center mb-12">
            <x-icon-logo class="w-16 h-16"/>
            <h1 class="ml-6 text-4xl font-bold text-green-700">peppool.space</h1>
        </div>

    </body>
</html>
