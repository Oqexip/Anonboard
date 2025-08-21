<?php

namespace App\Http\Controllers;

use App\Models\{Board, Thread};
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ThreadController extends Controller
{
    public function index(Board $board)
    {
        $threads = $board->threads()->orderByDesc('is_pinned')->orderByDesc('score')->latest()->paginate(20);
        return view('threads.index', compact('board', 'threads'));
    }


    public function show(Thread $thread)
    {
        $thread->load(['board', 'comments' => function ($q) {
            $q->orderByDesc('score')->latest();
        }]);
        return view('threads.show', compact('thread'));
    }


    public function store(Request $request, Board $board)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:140'],
            'content' => ['required', 'string', 'min:3', 'max:10000'],
        ]);
        $anonId = (int) $request->attributes->get('anon_id');
        $thread = Thread::create([
            'board_id' => $board->id,
            'anon_session_id' => $anonId,
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
        ]);
        return redirect()->route('threads.show', $thread);
    }


    public function destroy(Request $request, Thread $thread)
    {
        $anonId = (int) $request->attributes->get('anon_id');
        $isOwner = $thread->anon_session_id === $anonId;
        $recent = $thread->created_at->gt(now()->subMinutes(15));
        if ($isOwner && $recent) {
            $thread->delete();
            return back()->with('ok', 'Thread removed');
        }
        $this->authorize('delete', $thread); // for moderators
        $thread->delete();
        return back();
    }
}
