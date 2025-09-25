<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'board_id',
        'anon_session_id',
        'user_id',
        'title',
        'content',
        'edited_at',
        // jika punya kolom ini di DB:
        'score',
        'is_pinned',
    ];

    protected $casts = [
        'board_id'        => 'integer',
        'user_id'         => 'integer',
        'anon_session_id' => 'integer',
        'score'           => 'integer',
        'is_pinned'       => 'boolean',
        'edited_at'       => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    // ===== Relasi =====
    public function user()     { return $this->belongsTo(User::class); }
    public function anon()     { return $this->belongsTo(AnonSession::class, 'anon_session_id'); }
    public function board()    { return $this->belongsTo(Board::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function votes()    { return $this->morphMany(Vote::class, 'votable'); }
    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }

    // ===== Ownership & izin edit =====
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

    // ===== Voting & skor =====
    public function recalcScore(): void
    {
        $this->score = (int) $this->votes()->sum('value');
        $this->saveQuietly();
    }

    // ===== Pencarian =====

    /**
     * Deteksi dukungan FULLTEXT (MySQL).
     */
    public static function supportsFullText(): bool
    {
        $connection = config('database.default');
        $driver     = config("database.connections.$connection.driver");
        return $driver === 'mysql';
    }

    /**
     * Scope pencarian:
     * - MySQL FULLTEXT pada (title, content) dalam BOOLEAN MODE.
     * - Fallback LIKE jika bukan MySQL / index belum ada.
     */
    public function scopeSearch($query, string $q)
    {
        $q = trim($q);
        if ($q === '') return $query;

        if (self::supportsFullText()) {
            // sanit sederhana untuk boolean mode
            $term = preg_replace('/[^\p{L}\p{N}\s\+\-\*\~\"\(\)]/u', ' ', $q);

            return $query
                ->whereRaw("MATCH(title, content) AGAINST (? IN BOOLEAN MODE)", [$term.'*'])
                ->orderByRaw("MATCH(title, content) AGAINST (? IN BOOLEAN MODE) DESC", [$term.'*']);
        }

        // Escape % dan _ pada LIKE
        $like = '%'.addcslashes($q, '%_').'%';

        return $query->where(function ($w) use ($like) {
            $w->where('title', 'like', $like)
              ->orWhere('content', 'like', $like);
        });
    }

    // ===== (Opsional) Helper ringkasan untuk view =====
    public function getExcerptAttribute(): string
    {
        $plain = trim(strip_tags((string) $this->content));
        return str($plain)->limit(160)->toString();
    }
}
