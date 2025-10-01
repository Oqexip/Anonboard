<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ObrolIn' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="min-h-full bg-gradient-to-br from-slate-50 via-white to-slate-50 text-gray-900 antialiased">

    {{-- ===== NAVBAR ===== --}}
    <header class="sticky top-0 z-40 border-b border-white/40 bg-white/70 backdrop-blur">
        <div class="max-w-6xl mx-auto px-4">

            {{-- ===== DESKTOP (md+) ===== --}}
            <div class="h-14 hidden md:grid grid-cols-3 items-center">
                {{-- Kiri: Brand --}}
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                        <span
                            class="font-extrabold text-2xl tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600">
                            OmongIn
                        </span>
                    </a>
                </div>

                {{-- Tengah: Menu utama (center) --}}
                <nav class="flex items-stretch justify-center gap-6">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center text-sm px-1 pb-2 border-b-2 transition
                    {{ request()->routeIs('home') ? 'text-sky-700 border-sky-500' : 'text-slate-700 border-transparent hover:border-slate-300' }}">
                        Boards
                    </a>
                    <a href="{{ route('popular.index') }}"
                        class="inline-flex items-center text-sm px-1 pb-2 border-b-2 transition
                    {{ request()->routeIs('popular.*') ? 'text-sky-700 border-sky-500' : 'text-slate-700 border-transparent hover:border-slate-300' }}">
                        Popular
                    </a>
                </nav>

                {{-- Kanan: Auth --}}
                <nav class="flex items-center justify-end gap-2">
                    @auth
                        <span class="text-sm text-slate-600 mr-1">Hi, <span
                                class="font-medium">{{ auth()->user()->name }}</span></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="inline-flex items-center gap-2 px-3 h-9 rounded-xl text-white text-sm shadow-sm transition
                       bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-3 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm shadow-sm transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-3 h-9 rounded-xl text-white text-sm shadow-sm transition
                      bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                            Register
                        </a>
                    @endauth
                </nav>
            </div>

            {{-- ===== MOBILE (< md) ===== --}}
            <div class="md:hidden" x-data="{ open: false }" @keydown.escape.window="open=false">
                <div class="h-14 flex items-center justify-between">
                    {{-- Brand (mobile only) --}}
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                        <span
                            class="font-extrabold text-xl tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600">
                            OmongIn
                        </span>
                    </a>

                    {{-- Hamburger --}}
                    <button @click="open=!open"
                        class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="sr-only">Menu</span>
                    </button>
                </div>

                {{-- Sheet --}}
                <div x-show="open" x-cloak @click.outside="open=false"
                    class="absolute left-0 right-0 mt-2 rounded-2xl border border-slate-200 bg-white shadow-lg p-3">
                    {{-- Menu utama dengan underline saat aktif --}}
                    <a href="{{ route('home') }}"
                        class="block px-3 h-10 leading-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm shadow-sm mb-2 text-center
                    {{ request()->routeIs('home') ? 'text-sky-700 underline underline-offset-4' : 'text-slate-700' }}">
                        Boards
                    </a>
                    <a href="{{ route('popular.index') }}"
                        class="block px-3 h-10 leading-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm shadow-sm mb-3 text-center
                    {{ request()->routeIs('popular.*') ? 'text-sky-700 underline underline-offset-4' : 'text-slate-700' }}">
                        Popular
                    </a>

                    {{-- Auth --}}
                    @auth
                        <div class="px-2 py-2 text-sm text-slate-600">Hi, <span
                                class="font-medium">{{ auth()->user()->name }}</span></div>
                        <form method="POST" action="{{ route('logout') }}" class="px-2 pb-2">
                            @csrf
                            <button
                                class="w-full inline-flex items-center justify-center px-3 h-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm shadow-sm">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="block px-3 h-10 leading-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm shadow-sm mb-2 text-center">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="block px-3 h-10 leading-10 rounded-xl text-white text-sm text-center shadow-sm
                      bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                            Register
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </header>

    {{-- ===== CONTENT ===== --}}
    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('ok'))
            <div x-data="{ show: true }" x-show="show" x-cloak x-transition.opacity.duration.200ms
                class="mb-4 flex w-full items-center justify-between rounded-2xl border border-emerald-200/60 bg-emerald-50/80 px-4 py-3 text-emerald-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path
                            d="M12 2a10 10 0 1010 10A10.011 10.011 0 0012 2zm-1 15l-4-4 1.414-1.414L11 13.172l5.586-5.586L18 9z" />
                    </svg>
                    <span>{{ session('ok') ?? 'Posted' }}</span>
                </div>
                <button @click="show=false"
                    class="ml-4 inline-flex items-center justify-center rounded-lg px-2 py-1 hover:bg-emerald-100/70"
                    aria-label="Tutup">&times;</button>
            </div>
        @endif

        {{ $slot }}
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-white/60 backdrop-blur">
        <div class="h-1 w-full bg-gradient-to-r from-sky-500 via-indigo-500 to-fuchsia-500"></div>
        <div
            class="max-w-6xl mx-auto px-4 py-6 text-sm text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>© {{ date('Y') }} ObrolIn</p>
            <p>
                <span class="text-slate-400">Made with ❤️ by</span>
                <span
                    class="font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600">Oqexip</span>
            </p>
        </div>
    </footer>

    {{-- Alpine (hapus jika sudah di-bundle di app.js) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
