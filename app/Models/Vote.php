<?php

// app/Models/Vote.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model
{
    protected $fillable = [
        'votable_type', 'votable_id',
        'user_id', 'anon_key',
        'value',
    ];

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }
}
