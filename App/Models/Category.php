<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['board_id', 'name', 'slug'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
