<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Comment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['thread_id', 'parent_id', 'anon_session_id', 'content', 'depth'];


    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
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
