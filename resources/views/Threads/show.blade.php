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

    {{-- THREAD CARD (tetap seperti sebelumnya) --}}
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

            {{-- … tombol vote & delete thread … --}}
            {{-- Hapus thread: admin / pemilik (policy) --}}
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
    </article>

    {{-- FORM KOMENTAR BARU --}}
    @if (!$thread->is_locked)
        <section class="bg-white border rounded-xl p-5 mb-6">
            <form action="{{ route('comments.store', $thread) }}" method="POST" class="space-y-2">
                @csrf
                <textarea name="content" rows="4" class="w-full rounded-md" placeholder="Write a comment" required></textarea>
                <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white">Comment</button>
            </form>
        </section>
    @else
        <div class="mb-6 rounded-md bg-gray-100 border px-3 py-2 text-gray-600">This thread is locked.</div>
    @endif

    {{-- LIST KOMENTAR (root) --}}
    @php($grouped = isset($grouped) ? $grouped : $thread->comments->groupBy('parent_id'))
    <section class="space-y-3">
        @foreach ($grouped[null] ?? [] as $c)
            @include('threads.partials.comment', [
                'c' => $c,
                'grouped' => $grouped,
                'thread' => $thread,
            ])
        @endforeach
    </section>
</x-app-layout>
