<x-layouts.app :title="$thread->title ?? 'Thread'">
    <article class="bg-white border rounded-xl p-5 mb-4">
        <header class="mb-2 flex items-center justify-between">
            <h1 class="text-xl font-bold">{{ $thread->title ?? 'Thread' }}</h1>
            <div class="flex items-center gap-2 text-sm">
                <button x-data
                    x-on:click="fetch('/vote',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({type:'thread',id:'{{ $thread->id }}',value:'1'})}).then(r=>r.json()).then(d=>($refs.score.textContent=d.score))"
                    class="px-2 py-1 rounded bg-gray-100">▲</button>
                <span x-ref="score">{{ $thread->score }}</span>
                <button x-data
                    x-on:click="fetch('/vote',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({type:'thread',id:'{{ $thread->id }}',value:'-1'})}).then(r=>r.json()).then(d=>($refs.score.textContent=d.score))"
                    class="px-2 py-1 rounded bg-gray-100">▼</button>
            </div>
        </header>
        <div class="prose max-w-none">{!! \App\Support\Sanitize::toHtml($thread->content) !!}</div>
    </article>


    <section class="bg-white border rounded-xl p-5 mb-4">
        <form action="{{ url('/t/' . $thread->id . '/comments') }}" method="post" class="space-y-2">
            @csrf
            <textarea name="content" rows="4" class="w-full rounded-md" placeholder="Write a comment" required></textarea>
            <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white">Comment</button>
        </form>
    </section>


    <section class="space-y-3">
        @foreach ($thread->comments as $c)
            <div class="bg-white border rounded-xl p-4" style="margin-left: {{ $c->depth * 16 }}px">
                <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
                    @php($h = \App\Support\Anon::handleForThread($thread->id, $c->anon_session_id))
                    <span
                        class="font-semibold text-{{ ['rose', 'orange', 'amber', 'emerald', 'teal', 'sky', 'violet', 'pink'][$h['color']] }}-600">{{ $h['name'] }}</span>
                    <span>•</span>
                    <time>{{ $c->created_at->diffForHumans() }}</time>
                    <button x-data
                        x-on:click="fetch('/vote',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({type:'comment',id:'{{ $c->id }}',value:'1'})}).then(r=>r.json()).then(d=>($refs.s{{ $c->id }}.textContent=d.score))"
                        class="ml-auto px-2 py-1 rounded bg-gray-100">▲</button>
                    <span x-ref="s{{ $c->id }}">{{ $c->score }}</span>
                    <button x-data
                        x-on:click="fetch('/vote',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({type:'comment',id:'{{ $c->id }}',value:'-1'})}).then(r=>r.json()).then(d=>($refs.s{{ $c->id }}.textContent=d.score))"
                        class="px-2 py-1 rounded bg-gray-100">▼</button>
                </div>
                <div class="prose max-w-none">{!! \App\Support\Sanitize::toHtml($c->content) !!}</div>
            </div>
        @endforeach
    </section>
</x-layouts.app>
