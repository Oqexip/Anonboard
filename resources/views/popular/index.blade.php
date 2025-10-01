{{-- resources/views/popular/index.blade.php --}}
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $presets = [1 => '24h', 7 => '7d', 30 => '30d', 90 => '90d'];
@endphp

<x-layouts.app title="Popular Threads">
    <div class="max-w-5xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">🔥 Popular Threads</h1>

        {{-- Filter waktu --}}
        {{-- <div class="flex flex-wrap items-center gap-2 mb-6">
            @foreach ($presets as $d => $label)
                <a href="{{ route('popular.index', ['t' => $d]) }}"
                   class="inline-flex items-center px-3 h-9 rounded-xl border text-sm shadow-sm transition
                          {{ $days == $d ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
            <span class="h-5 w-px bg-slate-200 mx-1"></span>
            <form method="GET" action="{{ route('popular.index') }}" class="flex items-center gap-2">
                <label for="t" class="text-sm text-slate-600">Custom:</label>
                <input id="t" name="t" type="number" min="1" max="365" value="{{ $days }}"
                       class="w-20 h-9 rounded-xl border border-slate-200 bg-white px-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                <button class="inline-flex items-center px-3 h-9 rounded-xl text-sm shadow-sm
                               bg-gradient-to-r from-sky-500 to-fuchsia-600 text-white hover:from-sky-600 hover:to-fuchsia-700">
                    Apply
                </button>
            </form>
        </div> --}}

        @if ($threads->isEmpty())
            <p class="text-gray-500">Belum ada thread populer untuk periode ini.</p>
        @else
            <div class="space-y-5">
                @foreach ($threads as $thread)
                    @php
                        $title    = $thread->title ?? '(untitled)';
                        $excerpt  = Str::limit(strip_tags($thread->content), 220);
                        $userVote = $thread->user_vote ?? 0;

                        // Ambil attachment pertama (thumbnail) bila ada
                        $firstAttachment = $thread->attachments->first() ?? null;
                        $imgUrl = $firstAttachment ? Storage::url($firstAttachment->path) : null;
                    @endphp

                    <article
                        x-data="{
                            busy:false,
                            score: {{ $thread->score }},
                            myVote: {{ $userVote }},
                            async vote(val) {
                                if (this.busy) return;
                                this.busy = true;
                                try {
                                    const res = await fetch('{{ route('vote.store') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        },
                                        body: JSON.stringify({
                                            votable_type: 'thread',
                                            votable_id: {{ $thread->id }},
                                            value: val
                                        })
                                    });
                                    const data = await res.json();
                                    if (typeof data.score !== 'undefined') this.score = data.score;
                                    if (typeof data.myVote !== 'undefined') this.myVote = data.myVote;
                                } catch(e) {} finally { this.busy = false; }
                            }
                        }"
                        class="p-6 bg-white rounded-2xl shadow border border-slate-100">

                        {{-- Header: Judul --}}
                        <a href="{{ route('threads.show', $thread) }}"
                           class="block text-2xl font-bold text-slate-900 hover:underline">
                            {{ $title }}
                        </a>

                        {{-- Submeta: Nama board + waktu --}}
                        <div class="mt-1 text-slate-500 font-medium">
                            {{ $thread->board->name ?? 'Board' }}
                            <span class="mx-2">•</span>
                            <span class="font-normal">{{ $thread->created_at->diffForHumans() }}</span>
                        </div>

                        {{-- Isi ringkas --}}
                        @if($excerpt)
                            <p class="mt-4 text-slate-800">{{ $excerpt }}</p>
                        @endif

                        {{-- Gambar (dari attachment pertama) --}}
                        @if ($imgUrl)
                            <img src="{{ $imgUrl }}" alt="Image of {{ $title }}"
                                 class="mt-4 rounded-xl max-w-md w-full object-cover">
                        @endif

                        {{-- Footer: kiri = Komentar, kanan = vote --}}
                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ route('threads.show', $thread) }}#comments"
                               class="inline-flex items-center gap-2 px-3 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm shadow-sm">
                                💬 Komentar
                            </a>

                            <div class="flex items-center gap-3">
                                {{-- Upvote --}}
                                <button @click="vote(1)" :disabled="busy"
                                        class="h-8 w-8 grid place-items-center rounded-md border shadow-sm transition"
                                        :class="myVote === 1 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                    ▲
                                </button>

                                <span class="w-6 text-center text-slate-800 font-semibold" x-text="score"></span>

                                {{-- Downvote --}}
                                <button @click="vote(-1)" :disabled="busy"
                                        class="h-8 w-8 grid place-items-center rounded-md border shadow-sm transition"
                                        :class="myVote === -1 ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                                    ▼
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $threads->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
