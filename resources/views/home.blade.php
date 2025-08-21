<x-layouts.app>
    <h1 class="text-2xl font-bold mb-4">Boards</h1>
    <div class="grid sm:grid-cols-2 gap-4">
        @foreach ($boards as $b)
            <a href="{{ route('boards.show', $b) }}" class="block p-4 rounded-xl bg-white shadow border hover:shadow-md">
                <div class="text-lg font-semibold">/{{ $b->slug }}</div>
                <div class="text-sm text-gray-600">{{ $b->description }}</div>
            </a>
        @endforeach
    </div>
</x-layouts.app>
