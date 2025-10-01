<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class PopularController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->query('t', 30);
        if ($days < 1)
            $days = 1;
        if ($days > 365)
            $days = 365;

        $popularityExpr = '(threads.score * 2 + threads.comment_count)';

        $threads = Thread::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->with(['board', 'attachments']) // 👈 penting!
            ->orderByRaw("$popularityExpr DESC")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('popular.index', [
            'threads' => $threads,
            'days' => $days,
        ]);
    }

}
