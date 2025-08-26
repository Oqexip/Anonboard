<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AnonBoard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <header class="border-b bg-white">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="font-bold">AnonBoard</a>

            <div class="ml-auto flex items-center gap-3">
                @auth
                    <span class="text-sm text-gray-600">Hi, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="px-3 py-1 rounded bg-gray-200">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1 rounded bg-gray-200">Login</a>
                    <a href="{{ route('register') }}" class="px-3 py-1 rounded bg-emerald-600 text-white">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('ok'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                class="mb-3 flex justify-between items-center rounded-md bg-emerald-50 border border-emerald-200 px-3 py-2 text-emerald-800">
                <span>{{ session('ok') }}</span>
                <button type="button" @click="show = false"
                    class="ml-3 text-emerald-700 hover:text-emerald-900 font-bold">
                    ×
                </button>
            </div>
        @endif

        {{ $slot }}
    </main>

    {{-- Gunakan salah satu: jika tidak memakai import Alpine di app.js, aktifkan CDN di bawah --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
