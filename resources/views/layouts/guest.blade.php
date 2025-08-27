<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-sky-50 via-white to-fuchsia-50 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            
            {{-- Logo / Brand --}}
            <div class="flex flex-col items-center space-y-2">
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="w-16 h-16 text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600" />
                    <span class="text-2xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600">
                        {{ config('app.name', 'AnonBoard') }}
                    </span>
                </a>
                <p class="text-slate-500 text-sm">Welcome back, please sign in</p>
            </div>

            {{-- Card --}}
            <div class="w-full sm:max-w-md mt-8 px-6 py-6 bg-white/80 backdrop-blur rounded-2xl shadow-lg border border-slate-200/60 ">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
