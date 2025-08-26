@php use Illuminate\Support\Str; @endphp

<x-app-layout :title="'/' . $board->slug">

    {{-- Page header --}}
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold">/{{ $board->slug }}</h1>
    </div>

    {{-- Post form --}}
    <form action="{{ route('threads.store', $board) }}" method="POST" enctype="multipart/form-data"
        class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        @csrf

        <input name="title" placeholder="Title (optional)" class="sm:w-80 w-full rounded-md">

        <input name="content" placeholder="Say something..." required class="flex-1 rounded-md">

        <div class="flex items-center gap-2">
            <input id="images-input" type="file" name="images[]" accept="image/*" multiple class="hidden"
                onchange="__updateFileLabel(this)" />

            <label for="images-input"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 cursor-pointer text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4c-1.103 0-2 .897-2 2v10a2 2 0 0 0 2 2h16c1.103 0 2-.897 2-2V8c0-1.103-.897-2-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                </svg>
                <span id="images-label" class="text-gray-700">Attach images</span>
            </label>

            {{-- clear selection --}}
            <button type="button" class="text-l text-gray-500 hover:text-gray-700"
                onclick="__clearFiles()">Clear</button>
        </div>

        <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white shrink-0">
            Post
        </button>
    </form>
    {{-- <-- make sure the form closes before the list --}}

    {{-- Threads list --}}
    <div class="space-y-3">
        @forelse ($threads as $t)
            <a href="{{ route('threads.show', $t) }}" class="block bg-white rounded-xl border p-4 hover:shadow">
                <div class="text-lg font-semibold">
                    {{ $t->title ?? Str::limit(strip_tags($t->content), 80) }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ $t->comment_count }} comments • score {{ $t->score }} •
                    {{ $t->created_at->diffForHumans() }}
                </div>
            </a>
        @empty
            <div class="text-gray-600">No threads yet</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $threads->links() }}</div>
    <script>
        function __updateFileLabel(input) {
            const label = document.getElementById('images-label');
            if (!label) return;

            const files = Array.from(input.files || []);
            if (files.length === 0) {
                label.textContent = 'Attach images';
                return;
            }

            // Tampilkan max 2 nama file, sisanya “+N”
            const names = files.slice(0, 2).map(f => f.name).join(', ');
            const more = files.length > 2 ? ` +${files.length - 2} more` : '';
            label.textContent = names + more;
        }

        function __clearFiles() {
            const input = document.getElementById('images-input');
            if (!input) return;
            input.value = '';
            __updateFileLabel(input);
        }
    </script>
</x-app-layout>
