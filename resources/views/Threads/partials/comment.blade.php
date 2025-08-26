{{-- threads/partials/comment.blade.php --}}
@props(['c', 'grouped', 'thread'])

<div class="relative pl-4 sm:pl-6" style="margin-left: {{ $c->depth * 12 }}px">
    <span class="absolute left-0 top-4 bottom-4 w-px bg-gray-200"></span>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200/60 p-3">
        <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
            @if ($c->user)
                <span class="font-semibold text-indigo-600">{{ $c->user->name }}</span>
            @else
                @php($h = \App\Support\Anon::handleForThread($thread->id, $c->anon_session_id))
                <span class="font-semibold text-{{ ['rose','orange','amber','emerald','teal','sky','violet','pink'][$h['color']] }}-600">
                    {{ $h['name'] }}
                </span>
            @endif
            <span>•</span>
            <time>{{ $c->created_at->diffForHumans() }}</time>

            {{-- vote --}}
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

        <div class="prose max-w-none mb-2">{!! \App\Support\Sanitize::toHtml($c->content) !!}</div>

        @if (!$thread->is_locked && $c->depth < 5)
            <div x-data="{ open:false }">
                <button class="text-sm text-emerald-700 hover:underline" x-on:click="open=!open">Reply</button>
                <form x-show="open" x-cloak class="mt-2 space-y-2"
                      action="{{ route('comments.store', $thread) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $c->id }}">
                    <textarea name="content" rows="3" class="w-full rounded-md" placeholder="Reply..." required></textarea>
                    <button class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white">Post reply</button>
                </form>
            </div>
        @endif
    </div>
</div>

{{-- CHILDREN --}}
@foreach ($grouped[$c->id] ?? [] as $child)
    @include('threads.partials.comment', ['c' => $child, 'grouped' => $grouped, 'thread' => $thread])
@endforeach
