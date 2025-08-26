<x-app-layout :title="$thread->title ?? 'Thread'">
    {{-- Flash / errors --}}
    @if ($errors->any())
        <div class="mb-3 rounded-md bg-rose-50 border border-rose-200 px-3 py-2 text-rose-800">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- THREAD CARD --}}
    <article class="bg-white border rounded-xl p-5 mb-6">
        <header class="mb-3 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold">{{ $thread->title ?? 'Thread' }}</h1>
                <div class="text-sm text-gray-600 flex items-center gap-2">
                    @if ($thread->user)
                        <span class="font-semibold text-indigo-600">{{ $thread->user->name }}</span>
                    @else
                        @php($h = \App\Support\Anon::handleForThread($thread->id, $thread->anon_session_id))
                        <span
                            class="font-semibold text-{{ ['rose', 'orange', 'amber', 'emerald', 'teal', 'sky', 'violet', 'pink'][$h['color']] }}-600">
                            {{ $h['name'] }}
                        </span>
                    @endif
                    <span>•</span>
                    <time>{{ $thread->created_at->diffForHumans() }}</time>
                </div>
            </div>

            @can('delete', $thread)
                <form method="POST" action="{{ route('threads.destroy', $thread) }}"
                    onsubmit="return confirm('Delete this thread?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ml-2 px-2 py-1 rounded bg-rose-600 text-white hover:bg-rose-700">
                        Delete
                    </button>
                </form>
            @endcan
        </header>

        <div class="prose max-w-none">{!! \App\Support\Sanitize::toHtml($thread->content) !!}</div>

        @if ($thread->attachments->isNotEmpty())
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($thread->attachments as $a)
                    <a href="{{ Storage::url($a->path) }}" target="_blank" class="block">
                        <img src="{{ Storage::url($a->path) }}" alt=""
                            class="rounded-lg max-h-60 object-cover w-full">
                    </a>
                @endforeach
            </div>
        @endif
    </article>

    {{-- FORM KOMENTAR BARU --}}
    @if (!$thread->is_locked)
        <section class="bg-white border rounded-xl p-5 mb-6">
            <form action="{{ route('comments.store', $thread) }}" method="POST" enctype="multipart/form-data"
                class="space-y-3">
                @csrf

                <textarea name="content" rows="4" class="w-full rounded-md" placeholder="Write a comment" required></textarea>

                {{-- Kontrol bawah: Attach + Clear (kiri) | Comment (kanan) --}}
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    {{-- kiri: attach + clear --}}
                    <div class="flex items-center gap-2">
                        <input id="images-input" type="file" name="images[]" accept="image/*" multiple class="hidden"
                            onchange="__updateFileLabel(this)" />

                        <label for="images-input"
                            class="inline-flex items-center gap-2 px-3 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 cursor-pointer text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4c-1.103 0-2 .897-2 2v10a2 2 0 0 0 2 2h16c1.103 0 2-.897 2-2V8c0-1.103-.897-2-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            </svg>
                            <span id="images-label" class="text-gray-700">Attach images</span>
                        </label>

                        <button type="button" class="h-10 px-2 text-sm text-gray-500 hover:text-gray-700"
                            onclick="__clearFiles()">Clear</button>
                    </div>

                    {{-- kanan: submit --}}
                    <button type="submit" class="h-10 px-4 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                        Comment
                    </button>
                </div>
            </form>
        </section>
    @endif

    {{-- LIST KOMENTAR --}}
    @php($grouped = isset($grouped) ? $grouped : $thread->comments->groupBy('parent_id'))
    <section class="space-y-3">
        @foreach ($grouped[null] ?? [] as $c)
            @include('threads.partials.comment', ['c' => $c, 'grouped' => $grouped, 'thread' => $thread])
        @endforeach
    </section>

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
