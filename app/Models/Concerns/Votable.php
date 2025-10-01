<?php

// app/Models/Concerns/Votable.php
namespace App\Models\Concerns;

use App\Models\Vote;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Votable
{
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    /** Ambil vote milik viewer saat ini (user atau anon_key) */
    public function currentViewerVote(?string $anonKey = null): ?Vote
    {
        if (!Auth::check()) {
            return $this->votes()->where('user_id', Auth::id())->first();
        }
        if ($anonKey) {
            return $this->votes()->where('anon_key', $anonKey)->first();
        }
        return null;
    }
}
