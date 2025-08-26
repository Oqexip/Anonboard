<?php

namespace App\Http\Controllers;

use App\Models\{Thread, Comment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\SaveImages;


class CommentController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        $data = $request->validate([
            'content'   => ['required', 'string', 'min:1', 'max:10000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'images.*'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        // Ambil parent di thread yang sama (supaya tidak bisa reply ke comment thread lain)
        $parentId = $data['parent_id'] ?? null;
        $parent   = null;
        if ($parentId) {
            $parent = Comment::where('id', $parentId)
                ->where('thread_id', $thread->id)
                ->firstOrFail(); // 404 jika parent tidak di thread ini
        }

        // Tentukan depth
        $depth = $parent ? ($parent->depth + 1) : 0;

        // (Opsional) batasi kedalaman, contoh maksimal 5
        // $depth = min($depth, 5);

        $comment = Comment::create([
            'thread_id'       => $thread->id,
            'parent_id'       => $parentId,
            'anon_session_id' => Auth::check() ? null : (int) $request->attributes->get('anon_id'),
            'user_id'         => Auth::id(),
            'depth'           => $depth,
            'content'         => $data['content'],
        ]);

        if ($request->hasFile('images')) {
            foreach (SaveImages::storeMany($request->file('images')) as $att) {
                $comment->attachments()->create($att);
            }
        }

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
