@php use Illuminate\Support\Str; @endphp
<x-app-layout :title="'/' . $board->slug">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold">/{{ $board->slug }}</h1>
        <form action="{{ route('threads.store', $board) }}" method="POST" class="flex gap-2">
            @csrf
            <input name="title" class="w-64 rounded-md" placeholder="Title (optional)">
            <input name="content" class="w-72 rounded-md" placeholder="Say something..." required>
            <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white">Post</button>
        </form>

    </div>


    <div class="space-y-3">
        @forelse($threads as $t)
            <a href="{{ route('threads.show', $t) }}" class="block bg-white rounded-xl border p-4 hover:shadow">
                <div class="text-lg font-semibold">{{ $t->title ?? Str::limit(strip_tags($t->content), 80) }}</div>
                <div class="text-sm text-gray-500">{{ $t->comment_count }} comments • score {{ $t->score }} •
                    {{ $t->created_at->diffForHumans() }}</div>
            </a>
        @empty
            <div class="text-gray-600">No threads yet</div>
        @endforelse
    </div>


    <div class="mt-4">{{ $threads->links() }}</div>
</x-app-layout>
