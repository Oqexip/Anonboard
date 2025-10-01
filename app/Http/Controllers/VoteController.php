<?php

// app/Http/Controllers/VoteController.php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'votable_type' => 'required|string',   // e.g. "thread", "post", "comment", "reply"
            'votable_id'   => 'required|integer',
            'value'        => 'required|in:-1,1',
        ]);

        // Map tipe sederhana ke FQCN model
        $map = [
            'thread'  => Thread::class,
            'comment' => Comment::class,
        ];

        $class = $map[strtolower($data['votable_type'])] ?? null;
        if (!$class) {
            abort(422, 'Invalid votable_type');
        }

        $value = (int) $data['value'];

        try {
            $model = $class::query()->findOrFail($data['votable_id']);
        } catch (ModelNotFoundException $e) {
            abort(404, 'Content not found');
        }

        // Identitas pemilih
        $userId  = Auth::id();
        $anonKey = $userId ? null : $request->session()->get('anon_key');

        return DB::transaction(function () use ($model, $value, $userId, $anonKey) {
            // Ambil vote existing
            $query = $model->votes();
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('anon_key', $anonKey);
            }

            /** @var \App\Models\Vote|null $existing */
            $existing = $query->lockForUpdate()->first(); // lock agar konsisten

            if (!$existing) {
                // create baru
                $model->votes()->create([
                    'user_id'  => $userId,
                    'anon_key' => $anonKey,
                    'value'    => $value,
                ]);
                $model->increment('score', $value);
                $model->refresh();
                return response()->json([
                    'status' => 'created',
                    'score'  => $model->score,
                    'myVote' => $value,
                ]);
            }

            if ((int)$existing->value === $value) {
                // toggle off (hapus vote)
                $existing->delete();
                $model->decrement('score', $value);
                $model->refresh();
                return response()->json([
                    'status' => 'deleted',
                    'score'  => $model->score,
                    'myVote' => 0,
                ]);
            }

            // ubah vote dari -1 <-> +1
            $delta = $value - (int)$existing->value; // +2 atau -2
            $existing->update(['value' => $value]);
            $model->increment('score', $delta);
            $model->refresh();

            return response()->json([
                'status' => 'updated',
                'score'  => $model->score,
                'myVote' => $value,
            ]);
        });
    }
}
