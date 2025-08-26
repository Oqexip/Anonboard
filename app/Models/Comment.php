<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Comment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['thread_id', 'parent_id', 'anon_session_id', 'user_id', 'depth', 'content'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function anon()
    {
        return $this->belongsTo(AnonSession::class, 'anon_session_id');
    }


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
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');        // supaya reply urut bawah parent;
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
