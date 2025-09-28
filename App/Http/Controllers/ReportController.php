<?php

namespace App\Http\Controllers;

use App\Models\{Report, Thread, Comment};
use Illuminate\Http\Request;


class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:thread,comment'],
            'id' => ['required', 'integer'],
            'reason' => ['required', 'in:spam,abuse,nsfw,other'],
            'notes' => ['nullable', 'string', 'max:500']
        ]);
        $anonId = (int) $request->attributes->get('anon_id');
        $cls = $data['type'] === 'thread' ? Thread::class : Comment::class;
        Report::create([
            'reportable_type' => $cls,
            'reportable_id' => $data['id'],
            'anon_session_id' => $anonId,
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
        ]);
        return back()->with('ok', 'Reported');
    }
}
