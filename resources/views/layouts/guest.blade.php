<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ 'MJ Engenharia' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-primary-900">
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        <x-notification />
    </body>
</html>
