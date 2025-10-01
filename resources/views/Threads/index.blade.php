{{-- ini untuk halaman board --}}
@php use Illuminate\Support\Str; @endphp

<x-app-layout :title="'/' . $board->slug">
    {{-- Header Board + tombol New Thread (pakai satu scope Alpine untuk state modal) --}}
    <div x-data="{ open: false }" class="mb-6">
        <div class="flex items-center justify-between">
            <a href="#"
                class="inline-flex items-center gap-2 text-transparent bg-clip-text bg-gradient-to-r from-sky-600 to-fuchsia-600 hover:from-sky-700 hover:to-fuchsia-700 text-2xl font-bold">
                /{{ $board->slug }}
            </a>

            <button @click="open = true"
                class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                + New Thread
            </button>
        </div>

        {{-- Teleport modal ke <body>; tetap memakai state "open" dari wrapper di atas --}}
        <template x-teleport="body">
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
                {{-- backdrop --}}
                <div class="absolute inset-0 bg-black/40" @click="open = false" aria-hidden="true"></div>

                {{-- panel modal --}}
                <div x-show="open" x-transition @keydown.escape.window="open = false"
                    class="relative z-50 bg-white p-6 rounded-xl w-full max-w-lg shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Buat Thread Baru</h2>
                        <button @click="open=false" class="p-1 rounded-lg hover:bg-slate-100"
                            aria-label="Tutup">✕</button>
                    </div>

                    <form method="POST" action="{{ route('threads.store', $board) }}" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        {{-- Title --}}
                        <input name="title" placeholder="Judul"
                            class="w-full rounded-xl border border-slate-200 bg-white/80 px-3.5 py-2.5 text-[15px]
               placeholder:text-slate-400 shadow-sm
               focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200" />

                        {{-- Category (styled select) --}}
                        @if (isset($categories) && $categories->count())
                            <div class="relative">
                                <select name="category_id"
                                    class="peer w-full appearance-none rounded-xl border border-slate-200 bg-white/80 px-3.5 py-2.5 pr-10
                       text-[15px] shadow-sm
                       focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                                    <option value="">Pilih kategori (opsional)</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id || request('category') === $cat->slug)>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- chevron --}}
                                <svg class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-500 peer-focus:text-sky-500"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.17l3.71-2.94a.75.75 0 01.92 1.18l-4.25 3.38a.75.75 0 01-.92 0L5.21 8.41a.75.75 0 01.02-1.2z" />
                                </svg>
                            </div>
                        @endif

                        {{-- Content --}}
                        <textarea name="content" placeholder="Isi thread" required rows="4"
                            class="w-full rounded-xl border border-slate-200 bg-white/80 px-3.5 py-2.5 text-[15px]
               placeholder:text-slate-400 shadow-sm
               focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-200"></textarea>

                        {{-- Attach images + preview --}}
                        <div class="space-y-2">
                            <input id="images-input-modal" type="file" name="images[]" accept="image/*" multiple
                                class="hidden" onchange="__updateModalImages(this)" />

                            <div class="flex flex-wrap items-center gap-3">
                                <label for="images-input-modal"
                                    class="inline-flex items-center gap-2 h-10 rounded-xl border border-slate-200 bg-white/80 px-3 text-sm
                          hover:bg-slate-50 shadow-sm cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                    </svg>
                                    <span id="images-label-modal" class="text-slate-700">Attach images</span>
                                </label>

                                <button type="button"
                                    class="h-10 px-3 rounded-xl border border-slate-200 bg-white/80 text-sm text-slate-600 hover:bg-slate-50 shadow-sm"
                                    onclick="__clearModalImages()">
                                    Clear
                                </button>
                            </div>

                            <div id="images-preview-modal" class="grid grid-cols-3 gap-2"></div>

                            <p class="text-xs text-slate-400">
                                Format: jpg, jpeg, png, webp, gif • Maks 4MB per gambar
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button"
                                class="h-10 px-4 rounded-xl border border-slate-200 bg-white/80 text-slate-700 hover:bg-slate-50 shadow-sm"
                                @click="open=false">
                                Batal
                            </button>
                            <button
                                class="h-10 px-5 rounded-xl text-white shadow-sm
                   bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                                Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    {{-- Search + Filter --}}
    <form action="{{ route('boards.show', $board) }}" method="GET" class="mb-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari thread..."
                    class="w-full h-11 shadow-sm rounded-xl border border-slate-300 bg-white/80 px-4 pr-10 text-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-300 transition" />
                @if (request('q'))
                    <a href="{{ route('boards.show', $board) }}"
                        class="absolute right-10 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">✕</a>
                @endif
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" aria-label="Cari">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" stroke-width="1.6" />
                        <path d="M20 20l-3.5-3.5" stroke-width="1.6" />
                    </svg>
                </button>
            </div>

            {{-- Category Filter --}}
            @if (!empty($categories) && $categories->count())
                <select name="category"
                    class="h-11 rounded-xl border border-slate-300 bg-white/80 px-3 text-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-300 transition sm:w-56">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button
                class="h-11 px-4 rounded-xl text-white shadow-sm bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                Filter
            </button>
        </div>
    </form>

    {{-- Category Pills --}}
    @if (!empty($categories) && $categories->count())
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('boards.show', $board) }}"
                class="px-3 py-1.5 rounded-full text-xs border {{ request('category') ? 'border-slate-200 text-slate-600 bg-white' : 'border-sky-200 text-sky-700 bg-sky-50' }}">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('boards.show', $board) }}?category={{ $cat->slug }}@if (request('q')) &q={{ urlencode(request('q')) }} @endif"
                    class="px-3 py-1.5 rounded-full text-xs border {{ request('category') === $cat->slug ? 'border-sky-200 text-sky-700 bg-sky-50' : 'border-slate-200 text-slate-600 bg-white' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Threads List --}}
    <div class="space-y-3">
        @forelse ($threads as $t)
            <a href="{{ route('threads.show', $t) }}" class="group hover-card hover-card--bold block p-4 transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-lg font-semibold text-slate-900">
                        <span
                            class="transition group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-sky-600 group-hover:to-fuchsia-600">
                            {{ $t->title ?? Str::limit(strip_tags($t->content), 80) }}
                        </span>

                        {{-- Badge kategori di bawah judul --}}
                        @if ($t->category)
                            <div class="mt-1">
                                <span
                                    class="inline-block text-[11px] px-2 py-0.5 rounded-full border border-sky-200 bg-sky-50 text-sky-700">
                                    {{ $t->category->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-1 text-sm text-slate-600 flex items-center gap-4 flex-wrap">
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-slate-500 group-hover:text-sky-600 transition" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M20 2H4a2 2 0 00-2 2v13.586L6.586 14H20a2 2 0 002-2V4a2 2 0 00-2-2z" />
                        </svg>
                        {{ $t->comment_count }} comments
                    </span>

                    <span class="inline-flex items-center gap-1">
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

    {{-- Helpers untuk preview gambar di modal --}}
    <script>
        function __updateModalImages(input) {
            const label = document.getElementById('images-label-modal');
            const preview = document.getElementById('images-preview-modal');
            const files = Array.from(input.files || []);

            if (label) {
                if (files.length === 0) label.textContent = 'Attach images';
                else {
                    const names = files.slice(0, 2).map(f => f.name).join(', ');
                    const more = files.length > 2 ? ` +${files.length - 2} more` : '';
                    label.textContent = names + more;
                }
            }

            if (!preview) return;
            preview.innerHTML = '';
            const toShow = files.slice(0, 6);
            toShow.forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    img.className = 'w-full h-24 object-cover rounded-md';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        function __clearModalImages() {
            const input = document.getElementById('images-input-modal');
            const label = document.getElementById('images-label-modal');
            const preview = document.getElementById('images-preview-modal');
            if (input) input.value = '';
            if (label) label.textContent = 'Attach images';
            if (preview) preview.innerHTML = '';
        }
    </script>
</x-app-layout>
