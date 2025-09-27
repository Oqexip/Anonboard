<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Board extends Model
{
    use HasFactory;
    protected $fillable = ['slug', 'name', 'description', 'is_nsfw'];
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
