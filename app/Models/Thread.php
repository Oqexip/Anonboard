<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Thread extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['board_id', 'anon_session_id', 'title', 'content'];


    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }


    public function recalcScore(): void
    {
        $this->score = (int) $this->votes()->sum('value');
        $this->saveQuietly();
    }
}
