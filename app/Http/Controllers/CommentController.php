<?php

namespace App\Http\Controllers;

use App\Models\{Thread, Comment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Support\SaveImages;

class CommentController extends Controller
{
    /**
     * Simpan komentar baru ke sebuah thread.
     */
    public function store(Request $request, Thread $thread)
    {
        $data = $request->validate([
            'content'   => ['required', 'string', 'min:1', 'max:10000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'images.*'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        // Pastikan parent (jika ada) masih di thread yang sama
        $parentId = $data['parent_id'] ?? null;
        $parent   = null;

        if ($parentId) {
            $parent = Comment::whereKey($parentId)
                ->where('thread_id', $thread->id)
                ->firstOrFail();
        }

        // Hitung depth (batasi jika perlu)
        $depth = $parent ? ($parent->depth + 1) : 0;
        // $depth = min($depth, 5);

        DB::transaction(function () use ($request, $thread, $data, $parentId, $depth) {
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

            // Sinkronkan counter di thread
            $thread->increment('comment_count');
        });

        return back()->with('ok', 'Posted');
    }

    /**
     * Update komentar (hanya pemilik & dalam jangka waktu yang diizinkan).
     * Kolom edited_at hanya diisi ketika content benar-benar berubah.
     */
    public function update(Request $request, Comment $comment)
    {
        // hanya pemilik + <=15 menit
        if (! $comment->isOwnedByRequest($request) || ! $comment->canEditNow()) {
            abort(403, 'You can only edit your own comment within 15 minutes.');
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:10000'],
            // 'images.*' => ['image','max:5120'], // jika ingin izinkan lampiran saat edit
        ]);

        // Simpan hanya jika ada perubahan & set edited_at
        $comment->fill(['content' => $data['content']]);

        if ($comment->isDirty('content')) {
            $comment->edited_at = now(); // <-- indikator edited yang akurat
        }

        $comment->save();

        // (opsional) tambah lampiran baru saat edit
        // if ($request->hasFile('images')) {
        //     foreach (SaveImages::storeMany($request->file('images')) as $att) {
        //         $comment->attachments()->create($att);
        //     }
        // }

        return back()->with('ok', 'Comment edited');
    }

    /**
     * Hapus komentar.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $anonId = (int) $request->attributes->get('anon_id');

        // pemilik: anon yang sama ATAU user yang sama
        $isOwner = ($comment->anon_session_id === $anonId)
            || (Auth::check() && $comment->user_id === Auth::id());

        // boleh self-delete kalau masih baru (<= 15 menit)
        $recent = $comment->created_at && $comment->created_at->gt(now()->subMinutes(15));

        if ($isOwner && $recent) {
            DB::transaction(function () use ($comment) {
                $comment->delete();
                $comment->thread()->decrement('comment_count');
            });

            return back()->with('ok', 'Comment removed');
        }

        // selain itu, pakai policy (admin/mod, dsb.)
        $this->authorize('delete', $comment);

        DB::transaction(function () use ($comment) {
            $comment->delete();
            $comment->thread()->decrement('comment_count');
        });

        return back()->with('ok', 'Comment removed');
    }
}
