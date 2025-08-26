<?php

namespace App\Http\Controllers;

use App\Models\{Thread, Comment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        if ($thread->is_locked) {
            abort(403, 'Locked');
        }

        $data = $request->validate([
            'content'   => ['required', 'string', 'min:1', 'max:10000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $depth = 0;
        if (!empty($data['parent_id'])) {
            $parent = Comment::findOrFail($data['parent_id']);
            $depth = min(5, $parent->depth + 1);
        }

        Comment::create([
            'thread_id'       => $thread->id,
            'parent_id'       => $data['parent_id'] ?? null,
            'anon_session_id' => (int) $request->attributes->get('anon_id'),
            'user_id'         => Auth::id(),              // null jika anon
            'depth'           => $depth,
            'content'         => $data['content'],
        ]);

        $thread->increment('comment_count');

        return back()->with('ok', 'Posted');
    }

    public function destroy(Request $request, Comment $comment)
    {
        $anonId = (int) $request->attributes->get('anon_id');

        // pemilik: anon yang sama ATAU user yang sama
        $isOwner = ($comment->anon_session_id === $anonId)
            || (Auth::check() && $comment->user_id === Auth::id());

        // boleh self-delete kalau masih baru (<= 15 menit)
        $recent = $comment->created_at && $comment->created_at->gt(now()->subMinutes(15));

        if ($isOwner && $recent) {
            $comment->delete();
            // sinkronkan counter pada thread (opsional tapi disarankan)
            $comment->thread()->decrement('comment_count');
            return back()->with('ok', 'Comment removed');
        }

        // selain itu, pakai policy (admin/mod, dsb.)
        $this->authorize('delete', $comment);

        $comment->delete();
        $comment->thread()->decrement('comment_count');

        return back()->with('ok', 'Comment removed');
    }
}
