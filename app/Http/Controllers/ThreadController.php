<?php

namespace App\Http\Controllers;

use App\Models\{Board, Thread};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\SaveImages;
use Illuminate\Validation\Rule;


class ThreadController extends Controller
{
    /**
     * Update thread
     * Syarat: pemilik dan <= 15 menit (atau gunakan policy sendiri).
     */
    public function update(Request $request, Thread $thread)
    {
        if (! $thread->isOwnedByRequest($request) || ! $thread->canEditNow()) {
            abort(403, 'You can only edit your own thread within 15 minutes.');
        }

        $data = $request->validate([
            'title'   => ['nullable', 'string', 'max:140'],
            'content' => ['required', 'string', 'min:3', 'max:10000'],
            // 'images.*' => ['image', 'max:5120'], // aktifkan jika izinkan tambah gambar saat edit
        ]);

        $thread->fill([
            'title'   => $data['title']   ?? null,
            'content' => $data['content'],
        ]);

        if ($thread->isDirty(['title', 'content'])) {
            $thread->edited_at = now();
        }

        $thread->save();

        // Opsional: simpan lampiran baru saat edit
        // if ($request->hasFile('images')) {
        //     foreach (SaveImages::storeMany($request->file('images')) as $att) {
        //         $thread->attachments()->create($att);
        //     }
        // }

        return back()->with('ok', 'Thread updated');
    }

    /**
     * Index threads per board + search ?q=
     * Urutan:
     * - is_pinned desc selalu di depan
     * - jika q ada dan FULLTEXT: urut berdasar skor fulltext desc
     * - jika q ada dan fallback LIKE: created_at desc
     * - jika q kosong: score desc lalu created_at desc
     */
public function index(Board $board, Request $request)
{
    $q         = trim((string) $request->query('q', ''));
    $category  = $request->query('category'); // slug kategori

    $threads = Thread::query()
        ->where('board_id', $board->id)
        ->with(['user:id,name', 'category:id,name,slug'])
        ->when($category, function ($query, $category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        })
        ->when($q !== '', function ($query) use ($q) {
            $like = '%'.addcslashes($q, '%_').'%';
            $query->where(function ($w) use ($like) {
                $w->where('title', 'like', $like)
                  ->orWhere('content', 'like', $like);
            });
        })
        ->orderByDesc('is_pinned')
        ->orderByDesc('score')
        ->latest()
        ->paginate(20)
        ->withQueryString();

    $categories = $board->categories()->select('id','name','slug')->get();

    return view('threads.index', [
        'board'      => $board,
        'threads'    => $threads,
        'categories' => $categories,
        'q'          => $q,
        'category'   => $category,
        'title'      => $q ? "Hasil untuk “{$q}” di {$board->name}" : $board->name,
    ]);
}


    /**
     * Show thread + komentar terstruktur
     */
    public function show(Thread $thread)
    {
        $thread->load(['user', 'comments.user']); // eager load

        // Tampilkan komentar dari lama ke baru per parent
        $comments = $thread->comments()->orderBy('created_at')->get();
        $grouped  = $comments->groupBy('parent_id'); // kunci parent_id

        return view('threads.show', compact('thread', 'grouped'));
    }

    /**
     * Store thread baru di board
     */
public function store(Request $request, Board $board)
{
    $data = $request->validate([
        'title'        => ['nullable', 'string', 'max:140'],
        'content'      => ['required', 'string', 'min:3', 'max:10000'],
        // kategori opsional, tapi harus ada di tabel categories DAN milik board yang sama
        'category_id'  => [
            'nullable',
            'integer',
            Rule::exists('categories', 'id')->where('board_id', $board->id),
        ],
        'images.*'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
    ]);

    $thread = Thread::create([
        'board_id'        => $board->id,
        'category_id'     => $data['category_id'] ?? null, // <= simpan kategori
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


    /**
     * Destroy thread
     * Anon owner boleh hapus <= 15 menit, selain itu pakai policy (moderator/admin).
     * Setelah hapus, redirect ke board agar tidak 404.
     */
    public function destroy(Request $request, Thread $thread)
    {
        $board  = $thread->board; // simpan sebelum delete
        $anonId = (int) $request->attributes->get('anon_id');

        $isOwner = $thread->anon_session_id === $anonId;
        $recent  = $thread->created_at->gt(now()->subMinutes(15));

        if ($isOwner && $recent) {
            $thread->delete();

            return redirect()
                ->route('boards.show', $board)
                ->with('ok', 'Thread removed');
        }

        // moderator/admin via policy
        $this->authorize('delete', $thread);

        $thread->delete();

        return redirect()
            ->route('boards.show', $board)
            ->with('ok', 'Thread removed');
    }
}
