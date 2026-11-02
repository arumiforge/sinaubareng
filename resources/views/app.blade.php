<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('common.app_name') }}</title>
        @routes('app')
        @vite('resources/js/app.ts')
        @inertiaHead
    </head>
    <body class="bg-bg font-sans text-text antialiased">
        @inertia
    </body>
</html>
