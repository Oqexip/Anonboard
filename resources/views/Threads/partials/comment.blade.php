{{-- resources/views/threads/partials/comment.blade.php --}}
@props(['c', 'grouped', 'thread'])

@php
    use Illuminate\Support\Facades\Storage;

    $isAuthUser = (bool) $c->user;
    $createdHuman = $c->created_at->diffForHumans();

    $anon = !$isAuthUser ? \App\Support\Anon::handleForThread($thread->id, $c->anon_session_id) : null;
    $palette = ['rose', 'orange', 'amber', 'emerald', 'teal', 'sky', 'violet', 'pink'];
    $color = isset($anon['color'], $palette[$anon['color']]) ? $palette[$anon['color']] : 'gray';

    $canEditComment = $c->canEditNow() && $c->isOwnedByRequest(request());
    $uid = 'cmt-' . $c->id;
@endphp

<div id="c-{{ $c->id }}" class="relative pl-4 sm:pl-6"
    style="--depth: {{ (int) $c->depth }}; margin-left: calc(var(--depth) * 12px)">
    <span class="absolute left-0 top-4 bottom-4 w-px bg-gray-200"></span>

    <article class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200/60 p-3" x-data="commentItem({ id: {{ $c->id }}, content: @js($c->content) })"
        @keydown.escape.window="menuOpen=false; openReply=false; openEdit=false">

        {{-- HEADER --}}
        <header class="mb-2 flex items-start gap-3 text-sm text-gray-600">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    @if ($isAuthUser)
                        <span class="font-semibold text-indigo-600">{{ $c->user->name }}</span>
                    @else
                        <span class="font-semibold {{ 'text-' . $color . '-600' }}">{{ $anon['name'] ?? 'Anon' }}</span>
                    @endif

                    <span aria-hidden="true">•</span>
                    <time datetime="{{ $c->created_at->toIso8601String() }}">{{ $createdHuman }}</time>

                    @if ($c->edited_at)
                        <span class="text-gray-400 text-xs ml-1"
                            title="Last edited {{ $c->edited_at->diffForHumans() }}">(edited)</span>
                    @endif

                    {{-- ACTION MENU --}}
                    <div class="ml-auto relative" x-data="{ open: false }" @click.outside="open=false">
                        <button type="button" @click="open=!open"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak
                            class="absolute right-0 z-20 mt-2 w-44 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1">
                            @if ($canEditComment)
                                <button type="button" @click="open=false; openEdit=true"
                                    class="w-full px-3 py-2 text-left text-sm rounded-b-xl hover:bg-gray-50 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                                    </svg>
                                    Edit
                                </button>
                            @endif

                            <button
                                class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center gap-2 rounded-xl"
                                type="button" x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText('{{ route('threads.show', $thread) }}#c-{{ $c->id }}'); copied = true; setTimeout(() => copied=false, 2000)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V6a2 2 0 00-2-2h-2l-2-2H8a2 2 0 00-2 2v2H4v12a2 2 0 002 2h2" />
                                </svg>
                                <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
                            </button>

                            @can('delete', $c)
                                <form method="POST" action="{{ route('comments.destroy', $c) }}"
                                    onsubmit="return confirm('Delete this comment?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="w-full px-3 py-2 text-left text-sm hover:bg-rose-50 text-rose-600">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- KONTEN --}}
        <div class="prose max-w-none">{!! \App\Support\Sanitize::toHtml($c->content) !!}</div>

        @if ($c->attachments->isNotEmpty())
            <div class="mt-3 grid grid-cols-2 gap-2">
                @foreach ($c->attachments as $a)
                    @php $url = Storage::url($a->path); @endphp
                    <a href="{{ $url }}" target="_blank" class="block">
                        <img src="{{ $url }}" alt="" class="rounded-md max-h-40 object-cover w-full"
                            loading="lazy">
                    </a>
                @endforeach
            </div>
        @endif

        {{-- FOOTER: Reply (kiri) + Vote (kanan), rata kanan --}}
        <footer class="mt-3 pt-2 border-t border-gray-200 flex items-center justify-end gap-3">
            <button type="button" @click="openReply=true"
                class="inline-flex items-center gap-2 px-3 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2v10z" />
                </svg>
                Reply
            </button>

            <x-vote :model="$c" type="comment" />
        </footer>

        {{-- ===== MODAL REPLY ===== --}}
        @if (!$thread->is_locked && $c->depth < 5)
            <template x-teleport="body">
                <div x-show="openReply" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="openReply=false" aria-hidden="true"></div>

                    <div x-show="openReply" x-transition
                        class="relative z-10 bg-white p-6 rounded-2xl w-full max-w-xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold">Balas Komentar</h2>
                            <button @click="openReply=false" class="p-1 rounded-lg hover:bg-slate-100"
                                aria-label="Tutup">✕</button>
                        </div>

                        <form method="POST" action="{{ route('comments.store', $thread) }}"
                            enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $c->id }}">

                            <textarea name="content" rows="4" placeholder="Tulis balasan…" required
                                class="w-full rounded-xl border border-slate-200 bg-white/80 px-3.5 py-2.5 text-[15px]
                       placeholder:text-slate-400 shadow-sm focus:outline-none
                       focus:border-sky-400 focus:ring-2 focus:ring-sky-200"></textarea>

                            <div class="space-y-2">
                                <input id="images-input-{{ $uid }}" type="file" name="images[]"
                                    accept="image/*" multiple class="hidden"
                                    onchange="__updateReplyImages('{{ $uid }}', this)" />

                                <div class="flex flex-wrap items-center gap-3">
                                    <label for="images-input-{{ $uid }}"
                                        class="inline-flex items-center gap-2 h-10 rounded-xl border border-slate-200 bg-white/80 px-3 text-sm
                           hover:bg-slate-50 shadow-sm cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-600"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4L7.586 6H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                        </svg>
                                        <span id="images-label-{{ $uid }}" class="text-slate-700">Attach
                                            images</span>
                                    </label>

                                    <button type="button"
                                        class="h-10 px-3 rounded-xl border border-slate-200 bg-white/80 text-sm text-slate-600 hover:bg-slate-50 shadow-sm"
                                        onclick="__clearReplyImages('{{ $uid }}')">
                                        Clear
                                    </button>
                                </div>

                                <div id="images-preview-{{ $uid }}" class="grid grid-cols-3 gap-2"></div>

                                <p class="text-xs text-slate-400">
                                    Format: jpg, jpeg, png, webp, gif • Maks 4MB per gambar
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button"
                                    class="h-10 px-4 rounded-xl border border-slate-200 bg-white/80 text-slate-700 hover:bg-slate-50 shadow-sm"
                                    @click="openReply=false">Batal</button>
                                <button
                                    class="h-10 px-5 rounded-xl text-white shadow-sm
                         bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                                    Post reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        @endif

        {{-- ===== MODAL EDIT (baru) ===== --}}
        @if ($canEditComment)
            <template x-teleport="body">
                <div x-show="openEdit" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/40" @click="openEdit=false" aria-hidden="true"></div>

                    <div x-show="openEdit" x-transition
                        class="relative z-10 bg-white p-6 rounded-2xl w-full max-w-xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold">Edit Komentar</h2>
                            <button @click="openEdit=false" class="p-1 rounded-lg hover:bg-slate-100"
                                aria-label="Tutup">✕</button>
                        </div>

                        <form method="POST" action="{{ route('comments.update', $c) }}" class="space-y-4">
                            @csrf @method('PATCH')

                            <textarea name="content" rows="5" x-model="content" required
                                class="w-full rounded-xl border border-slate-200 bg-white/80 px-3.5 py-2.5 text-[15px]
                       placeholder:text-slate-400 shadow-sm focus:outline-none
                       focus:border-sky-400 focus:ring-2 focus:ring-sky-200"></textarea>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button"
                                    class="h-10 px-4 rounded-xl border border-slate-200 bg-white/80 text-slate-700 hover:bg-slate-50 shadow-sm"
                                    @click="openEdit=false">Batal</button>
                                <button
                                    class="h-10 px-5 rounded-xl text-white shadow-sm
                         bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        @endif
    </article>
</div>

{{-- Children Recursive --}}
@foreach ($grouped[$c->id] ?? [] as $child)
    @include('threads.partials.comment', ['c' => $child, 'grouped' => $grouped, 'thread' => $thread])
@endforeach

@once
    <script>
        function commentItem({
            id,
            content
        }) {
            return {
                id,
                content,
                openReply: false,
                openEdit: false,
                menuOpen: false
            };
        }

        // Helpers preview gambar di modal reply
        function __updateReplyImages(uid, inputEl) {
            const label = document.getElementById('images-label-' + uid);
            const preview = document.getElementById('images-preview-' + uid);
            const files = Array.from(inputEl.files || []);
            if (label) {
                label.textContent = files.length === 0 ?
                    'Attach images' :
                    files.slice(0, 2).map(f => f.name).join(', ') + (files.length > 2 ? ` +${files.length-2} more` : '');
            }
            if (!preview) return;
            preview.innerHTML = '';
            files.slice(0, 6).forEach(file => {
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

        function __clearReplyImages(uid) {
            const i = document.getElementById('images-input-' + uid);
            const l = document.getElementById('images-label-' + uid);
            const p = document.getElementById('images-preview-' + uid);
            if (i) i.value = '';
            if (l) l.textContent = 'Attach images';
            if (p) p.innerHTML = '';
        }
    </script>
@endonce
