<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AnonBoard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
    <header class="border-b bg-white">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="font-bold">AnonBoard</a>
            <nav class="space-x-4 text-sm">
                @foreach (\App\Models\Board::all() as $b)
                    <a class="hover:underline" href="{{ route('boards.show', $b) }}">/{{ $b->slug }}</a>
                @endforeach
            </nav>
        </div>
    </header>
    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('ok'))
            <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm">{{ session('ok') }}</div>
        @endif
        {{ $slot }}
    </main>
</body>

</html>
