<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    // allow edited_at to be saved
    protected $fillable = [
        'board_id',
        'anon_session_id',
        'user_id',
        'title',
        'content',
        'edited_at',   // <-- add this
    ];
    // If you prefer:  protected $guarded = [];

    public function user()   { return $this->belongsTo(User::class); }
    public function anon()   { return $this->belongsTo(AnonSession::class, 'anon_session_id'); }
    public function board()  { return $this->belongsTo(Board::class); }
    public function comments(){ return $this->hasMany(Comment::class); }
    public function votes()  { return $this->morphMany(Vote::class, 'votable'); }

    public function isOwnedByRequest(Request $request): bool
    {
        if ($this->user_id) {
            return optional($request->user())->id === $this->user_id;
        }
        $anonId = (int) ($request->attributes->get('anon_id') ?? session('anon_id'));
        return $anonId === (int) $this->anon_session_id;
    }

    public function canEditNow(): bool
    {
        return $this->created_at && $this->created_at->gt(now()->subMinutes(15));
    }

    public function recalcScore(): void
    {
        $this->score = (int) $this->votes()->sum('value');
        $this->saveQuietly();
    }

    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }
}
