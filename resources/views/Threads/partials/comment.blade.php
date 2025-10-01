{{-- resources/views/threads/partials/comment.blade.php --}}
@props(['c', 'grouped', 'thread'])

@php
    use Illuminate\Support\Facades\Storage;

    $isAuthUser   = (bool) $c->user;
    $createdHuman = $c->created_at->diffForHumans();

    $anon     = !$isAuthUser ? \App\Support\Anon::handleForThread($thread->id, $c->anon_session_id) : null;
    $palette  = ['rose', 'orange', 'amber', 'emerald', 'teal', 'sky', 'violet', 'pink'];
    $color    = isset($anon['color'], $palette[$anon['color']]) ? $palette[$anon['color']] : 'gray';

    $canEditComment = $c->canEditNow() && $c->isOwnedByRequest(request());
    $uid = 'cmt-' . $c->id;
@endphp

<div id="c-{{ $c->id }}" class="relative pl-4 sm:pl-6"
     style="--depth: {{ (int) $c->depth }}; margin-left: calc(var(--depth) * 12px)">
    <span class="absolute left-0 top-4 bottom-4 w-px bg-gray-200"></span>

    <div
        class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200/60 p-3"
        x-data="commentItem({
            id: {{ $c->id }},
            openEdit: false
        })"
    >
        {{-- HEADER --}}
        <div class="mb-2 flex items-start gap-3 text-sm text-gray-600">
            {{-- ✅ VOTE UNTUK COMMENT --}}

            {{-- Identitas + waktu + menu --}}
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
                        <span class="text-gray-400 text-xs ml-1" title="Last edited {{ $c->edited_at->diffForHumans() }}">(edited)</span>
                    @endif

                    {{-- ACTION MENU --}}
                    <div class="ml-auto relative" x-data="{ open: false }" @keydown.escape.window="open=false" @click.outside="open=false">
                        <button type="button"
                                @click="open = !open"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 focus:outline-none"
                                aria-haspopup="true" :aria-expanded="open">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                            </svg>
                            <span class="sr-only">Comment actions</span>
                        </button>

                        <div x-show="open" x-cloak
                             class="absolute right-0 z-20 mt-2 w-44 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1"
                             role="menu" tabindex="-1">
                            @if ($canEditComment)
                                <button type="button"
                                        @click="open=false; openEdit = !openEdit"
                                        class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center gap-2"
                                        role="menuitem" tabindex="-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                    </svg>
                                    <span x-show="!openEdit">Edit</span>
                                    <span x-show="openEdit" x-cloak>Cancel edit</span>
                                </button>
                            @endif

                            {{-- Copy link --}}
                            <button type="button"
                                    @click="open=false; navigator.clipboard.writeText('{{ route('threads.show', $thread) }}#c-{{ $c->id }}')"
                                    class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 flex items-center gap-2"
                                    role="menuitem" tabindex="-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3.9 12a3.9 3.9 0 013.9-3.9h2v1.8h-2a2.1 2.1 0 000 4.2h2v1.8h-2A3.9 3.9 0 013.9 12zm12.3-3.9h-2v1.8h2a2.1 2.1 0 010 4.2h-2v1.8h2a3.9 3.9 0 000-7.8z" />
                                    <path d="M8 13h8v-2H8v2z" />
                                </svg>
                                Copy link
                            </button>

                            @can('delete', $c)
                                <form method="POST" action="{{ route('comments.destroy', $c) }}"
                                      onsubmit="return confirm('Delete this comment?')">
                                    @csrf @method('DELETE')
                                    <button class="w-full px-3 py-2 text-left text-sm hover:bg-rose-50 text-rose-600 flex items-center gap-2"
                                            role="menuitem" tabindex="-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6 7h12v13a2 2 0 01-2 2H8a2 2 0 01-2-2V7zm3-4h6l1 1h4v2H4V4h4l1-1z" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === KONTEN (view vs edit) === --}}
        <div class="mb-2">
            <div x-show="!openEdit" x-cloak>
                <div class="prose max-w-none">{!! \App\Support\Sanitize::toHtml($c->content) !!}</div>

                @if ($c->attachments->isNotEmpty())
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach ($c->attachments as $a)
                            @php $url = Storage::url($a->path); @endphp
                            <a href="{{ $url }}" target="_blank" class="block">
                                <img src="{{ $url }}" alt="" class="rounded-md max-h-40 object-cover w-full" loading="lazy" decoding="async">
                            </a>
                        @endforeach
                    </div>
                @endif
            <x-vote :model="$c" type="comment" />

            </div>

            @if ($canEditComment)
                <form x-show="openEdit" x-cloak method="POST" action="{{ route('comments.update', $c) }}" class="space-y-2">
                    @csrf @method('PATCH')
                    <textarea name="content" rows="4"
                              class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('content', $c->content) }}</textarea>
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Save</button>
                        <button type="button" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200" @click="openEdit=false">Cancel</button>
                    </div>
                </form>
            @endif
        </div>

        {{-- REPLY FORM --}}
        @if (!$thread->is_locked && $c->depth < 5)
            <form action="{{ route('comments.store', $thread) }}" method="POST" enctype="multipart/form-data"
                  class="mt-2 space-y-2" x-show="!openEdit">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $c->id }}">
                <textarea name="content" rows="3"
                          class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Reply..." required></textarea>

                {{-- Kontrol bawah: Attach + Clear (kiri) | Post reply (kanan) --}}
                <div class="flex items-center justify-between gap-2 flex-nowrap">
                    {{-- Kiri: attach + clear --}}
                    <div class="flex items-center gap-2 min-w-0">
                        @php
                            $inputId = "images-input-{$uid}";
                            $labelId = "images-label-{$uid}";
                        @endphp

                        <input id="{{ $inputId }}" type="file" name="images[]" accept="image/*" multiple
                               class="hidden" @change="updateFileLabel($event, '{{ $labelId }}')" />

                        <label for="{{ $inputId }}"
                               class="inline-flex items-center gap-2 px-3 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 cursor-pointer text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4c-1.103 0-2 .897-2 2v10a2 2 0 0 0 2 2h16c1.103 0 2-.897 2-2V8c0-1.103-.897-2-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                            </svg>
                            <span id="{{ $labelId }}" class="text-gray-700 truncate">Attach images</span>
                        </label>

                        <button type="button" class="h-10 px-2 text-sm text-gray-500 hover:text-gray-700"
                                @click="clearFiles('{{ $inputId }}', '{{ $labelId }}')">
                            Clear
                        </button>
                    </div>

                    {{-- Kanan: tombol post (fixed width) --}}
                    <button type="submit"
                            class="flex-none h-10 px-5 rounded-lg text-white font-medium shadow-sm
                                   bg-gradient-to-r from-sky-500 to-fuchsia-600 hover:from-sky-600 hover:to-fuchsia-700">
                        Post reply
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Children Recursive --}}
@foreach ($grouped[$c->id] ?? [] as $child)
    @include('threads.partials.comment', ['c' => $child, 'grouped' => $grouped, 'thread' => $thread])
@endforeach

@once
    <script>
        function commentItem({ id, openEdit = false }) {
            return {
                id,
                openEdit,
                updateFileLabel(evt, labelId) {
                    const label = document.getElementById(labelId);
                    if (!label) return;
                    const files = Array.from(evt.target.files || []);
                    if (files.length === 0) {
                        label.textContent = 'Attach images';
                        return;
                    }
                    const names = files.slice(0, 2).map(f => f.name).join(', ');
                    const more = files.length > 2 ? ` +${files.length - 2} more` : '';
                    label.textContent = names + more;
                },
                clearFiles(inputId, labelId) {
                    const input = document.getElementById(inputId);
                    if (!input) return;
                    input.value = '';
                    this.updateFileLabel({ target: input }, labelId);
                },
            }
        }
    </script>
@endonce
