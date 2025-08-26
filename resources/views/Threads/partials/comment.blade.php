{{-- resources/views/threads/partials/comment.blade.php --}}
@props(['c', 'grouped', 'thread'])

<div class="relative pl-4 sm:pl-6" style="margin-left: {{ $c->depth * 12 }}px">
    <span class="absolute left-0 top-4 bottom-4 w-px bg-gray-200"></span>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200/60 p-3">
        {{-- header baris atas: author • waktu • vote • delete --}}
        <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
            @if ($c->user)
                <span class="font-semibold text-indigo-600">{{ $c->user->name }}</span>
            @else
                @php($h = \App\Support\Anon::handleForThread($thread->id, $c->anon_session_id))
                <span
                    class="font-semibold text-{{ ['rose', 'orange', 'amber', 'emerald', 'teal', 'sky', 'violet', 'pink'][$h['color']] }}-600">
                    {{ $h['name'] }}
                </span>
            @endif
            <span>•</span>
            <time>{{ $c->created_at->diffForHumans() }}</time>

            {{-- vote controls --}}
            <button x-data
                x-on:click="fetch('/vote',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({type:'comment',id:'{{ $c->id }}',value:'1'})}).then(r=>r.json()).then(d=>($refs.s{{ $c->id }}.textContent=d.score))"
                class="ml-auto px-2 py-1 rounded bg-gray-100">▲</button>
            <span x-ref="s{{ $c->id }}">{{ $c->score }}</span>
            <button x-data
                x-on:click="fetch('/vote',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({type:'comment',id:'{{ $c->id }}',value:'-1'})}).then(r=>r.json()).then(d=>($refs.s{{ $c->id }}.textContent=d.score))"
                class="px-2 py-1 rounded bg-gray-100">▼</button>

            @can('delete', $c)
                <form action="{{ route('comments.destroy', $c) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-2 py-1 rounded bg-rose-50 text-rose-700 hover:bg-rose-100">Delete</button>
                </form>
            @endcan
        </div>

        {{-- isi komentar --}}
        <div class="prose max-w-none mb-2">{!! \App\Support\Sanitize::toHtml($c->content) !!}</div>

        {{-- lampiran gambar komentar --}}
        @if ($c->attachments->isNotEmpty())
            <div class="mt-2 grid grid-cols-2 gap-2">
                @foreach ($c->attachments as $a)
                    <a href="{{ Storage::url($a->path) }}" target="_blank" class="block">
                        <img src="{{ Storage::url($a->path) }}" alt=""
                            class="rounded-md max-h-40 object-cover w-full">
                    </a>
                @endforeach
            </div>
        @endif

        {{-- reply form --}}
        @if (!$thread->is_locked && $c->depth < 5)
            <div x-data="{ open: false }">
                <form action="{{ route('comments.store', $thread) }}" method="POST" enctype="multipart/form-data"
                    class="mt-2 space-y-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $c->id }}">

                    <textarea name="content" rows="3" class="w-full rounded-md" placeholder="Reply..." required></textarea>

                    {{-- kiri: attach + clear | kanan: submit --}}
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <input id="images-input-{{ $c->id }}" type="file" name="images[]"
                                accept="image/*" multiple class="hidden"
                                onchange="__updateFileLabel(this, 'images-label-{{ $c->id }}')" />

                            <label for="images-input-{{ $c->id }}"
                                class="inline-flex items-center gap-2 px-3 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 cursor-pointer text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M20 6h-3.586l-1.707-1.707A.996.996 0 0 0 14 4h-4c-.265 0-.52.105-.707.293L7.586 6H4c-1.103 0-2 .897-2 2v10a2 2 0 0 0 2 2h16c1.103 0 2-.897 2-2V8c0-1.103-.897-2-2-2zM12 19a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                </svg>
                                <span id="images-label-{{ $c->id }}" class="text-gray-700">Attach images</span>
                            </label>

                            <button type="button" class="h-10 px-2 text-sm text-gray-500 hover:text-gray-700"
                                onclick="__clearFiles('images-input-{{ $c->id }}', 'images-label-{{ $c->id }}')">
                                Clear
                            </button>
                        </div>

                        <button type="submit"
                            class="h-10 px-4 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                            Post reply
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

{{-- children (rekursif) --}}
@foreach ($grouped[$c->id] ?? [] as $child)
    @include('threads.partials.comment', ['c' => $child, 'grouped' => $grouped, 'thread' => $thread])
@endforeach

@once
    <script>
        function __updateFileLabel(input, labelId) {
            const label = document.getElementById(labelId);
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

        function __clearFiles(inputId, labelId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.value = '';
            __updateFileLabel(input, labelId);
        }
    </script>
@endonce
