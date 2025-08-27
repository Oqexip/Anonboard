@php use Illuminate\Support\Str; @endphp

<x-app-layout :title="'/' . $board->slug">

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <a href="#"
            class="inline-flex items-center gap-2 text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600 hover:from-sky-700 hover:to-fuchsia-700 text-2xl font-bold"">/{{ $board->slug }}</a>
    </div>
    {{-- Post form --}}
    <form action="{{ route('threads.store', $board) }}" method="POST" enctype="multipart/form-data"
        class=" mb-8 bg-white/80 backdrop-blur rounded-2xl border border-slate-200 p-5 shadow-sm ">
        @csrf

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center"><input name="title" placeholder="Title (optional)"
                class="sm:w-80 w-full rounded-xl border border-slate-50 px-3 py-2
           focus:border-sky-500 focus:ring-sky-500" />

            <input name="content" placeholder="Say something..." required
                class="flex-1 rounded-xl border border-slate-50 px-3 py-2
           focus:border-sky-500 focus:ring-sky-500" />

            <div class="flex items-center gap-2"> <input id="images-input" type="file" name="images[]"
                    accept="image/*" multiple class="hidden" onchange="__updateFileLabel(this)" /> <label
                    for="images-input"
                    class="inline-flex items-center gap-2 px-3 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 cursor-pointer text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4c-1.103 0-2 .897-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8c0-1.103-.897-2-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                    </svg> <span id="images-label" class="text-slate-700">Attach images</span> </label> <button
                    type="button" class="px-2 h-10 text-sm text-slate-500 hover:text-slate-700"
                    onclick="__clearFiles()">Clear</button> </div> <button
                class="px-4 h-10 rounded-xl text-white shadow-sm bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700 shrink-0">
                Post </button>
        </div>
    </form>

    {{-- Threads list --}}
    <div class="space-y-3">
        @forelse ($threads as $t)
            <a href="{{ route('threads.show', $t) }}" class="group hover-card hover-card--bold block p-4 transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-lg font-semibold text-slate-900">
                        <span
                            class="transition group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-sky-600 group-hover:to-fuchsia-600">
                            {{ $t->title ?? Str::limit(strip_tags($t->content), 80) }}
                        </span>
                    </div>
                </div>

                <div class="mt-1 text-sm text-slate-600 flex items-center gap-4 flex-wrap">
                    <span class="inline-flex items-center gap-1">
                        {{-- comments icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-slate-500 group-hover:text-sky-600 transition" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M20 2H4a2 2 0 00-2 2v13.586L6.586 14H20a2 2 0 002-2V4a2 2 0 00-2-2z" />
                        </svg>
                        {{ $t->comment_count }} comments
                    </span>

                    <span class="inline-flex items-center gap-1">
                        {{-- time icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-slate-500 group-hover:text-sky-600 transition" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M12 1a11 11 0 1011 11A11.013 11.013 0 0012 1zm1 11H7V9h4V5h2z" />
                        </svg>
                        {{ $t->created_at->diffForHumans() }}
                    </span>
                </div>
            </a>
        @empty
            <div class="text-slate-600">No threads yet</div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $threads->links() }}
    </div>

    <script>
        function __updateFileLabel(input) {
            const label = document.getElementById('images-label');
            if (!label) return;

            const files = Array.from(input.files || []);
            if (files.length === 0) {
                label.textContent = 'Attach images';
                return;
            }

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
