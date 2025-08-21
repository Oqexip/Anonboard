<?php

namespace App\Http\Controllers;

use App\Models\{Thread, Comment};
use Illuminate\Http\Request;


class CommentController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        if ($thread->is_locked) {
            abort(403, 'Locked');
        }
        $data = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:10000'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);
        $anonId = (int) $request->attributes->get('anon_id');


        $depth = 0;
        if (!empty($data['parent_id'])) {
            $parent = Comment::findOrFail($data['parent_id']);
            $depth = min(5, $parent->depth + 1);
        }


        $comment = Comment::create([
            'thread_id' => $thread->id,
            'parent_id' => $data['parent_id'] ?? null,
            'anon_session_id' => $anonId,
            'depth' => $depth,
            'content' => $data['content'],
        ]);
        $thread->increment('comment_count');
        return back()->with('ok', 'Posted');
    }
}
