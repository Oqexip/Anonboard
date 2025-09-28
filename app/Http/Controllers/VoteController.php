<?php

namespace App\Http\Controllers;

use App\Models\{Vote, Thread, Comment};
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class VoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:thread,comment'],
            'id' => ['required', 'integer'],
            'value' => ['required', 'in:-1,1'],
        ]);
        $anonId = (int) $request->attributes->get('anon_id');
        $model = $data['type'] === 'thread' ? Thread::findOrFail($data['id']) : Comment::findOrFail($data['id']);


        $vote = Vote::updateOrCreate([
            'votable_type' => $model::class,
            'votable_id' => $model->id,
            'anon_session_id' => $anonId,
        ], ['value' => (int)$data['value']]);


        // Recompute score
        $model->recalcScore();
        return response()->json(['score' => $model->score]);
    }
}
