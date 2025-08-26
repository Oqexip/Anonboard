<?php

namespace App\Http\Controllers;

use App\Models\{Board, Thread};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Support\SaveImages;



class ThreadController extends Controller
{
    public function index(Board $board)
    {
        $threads = $board->threads()->orderByDesc('is_pinned')->orderByDesc('score')->latest()->paginate(20);
        return view('threads.index', compact('board', 'threads'));
    }


    public function show(Thread $thread)
    {
        $thread->load(['user', 'comments.user']); // eager load
        // urutkan agar root->child tampil dari lama ke baru (atau sesuaikan)
        $comments = $thread->comments()->orderBy('created_at')->get();
        $grouped  = $comments->groupBy('parent_id'); // kunci: parent_id

        return view('threads.show', compact('thread', 'grouped'));
    }


    public function store(Request $request, Board $board)
    {
        // validasi tambah:
        $data = $request->validate([
            'title'   => ['nullable', 'string', 'max:140'],
            'content' => ['required', 'string', 'min:3', 'max:10000'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'], // 4MB/each
        ]);

        $thread = Thread::create([
            'board_id'        => $board->id,
            'anon_session_id' => Auth::check() ? null : (int) $request->attributes->get('anon_id'),
            'user_id'         => Auth::id(),
            'title'           => $data['title'] ?? null,
            'content'         => $data['content'],
        ]);

        if ($request->hasFile('images')) {
            foreach (SaveImages::storeMany($request->file('images')) as $att) {
                $thread->attachments()->create($att);
            }
        }
        return redirect()->route('threads.show', $thread)->with('ok', 'Posted');
    }


    public function destroy(Request $request, Thread $thread)
    {
        // Simpan board tujuan SEBELUM delete
        $board = $thread->board; // pastikan relasi board() ada di model Thread
        $anonId = (int) $request->attributes->get('anon_id');
        $isOwner = $thread->anon_session_id === $anonId;
        $recent = $thread->created_at->gt(now()->subMinutes(15));
        if ($isOwner && $recent) {
            $thread->delete();
            return  redirect()
                ->route('boards.show', $board)  // /b/{slug}
                ->with('ok', 'Thread removed');
        }
        $this->authorize('delete', $thread); // for moderators
        $thread->delete();
        return redirect()
            ->route('boards.show', $board)      // kembali ke board, tidak 404
            ->with('ok', 'Thread removed');
    }
}
