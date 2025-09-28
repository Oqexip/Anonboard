<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'thread_id',
        'parent_id',
        'anon_session_id',
        'user_id',
        'depth',
        'content',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    // === Relationships ===
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
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }
    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }
    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }

    // === Ownership / permission helpers ===
    public function isOwnedByRequest(Request $request): bool
    {
        if ($this->user_id) {
            return $request->user()?->id === $this->user_id;
        }
        return (int) $request->attributes->get('anon_id') === (int) $this->anon_session_id;
    }

    public function canEditNow(): bool
    {
        return $this->created_at && $this->created_at->gt(now()->subMinutes(15));
    }

    // === Voting ===
    public function recalcScore(): void
    {
        $this->score = (int) $this->votes()->sum('value');
        $this->saveQuietly();
    }

    // === Nice-to-have: accessor untuk Blade ===
    protected function isEdited(): Attribute
    {
        return Attribute::make(
            get: fn() => ! is_null($this->edited_at)
        );
    }
}
