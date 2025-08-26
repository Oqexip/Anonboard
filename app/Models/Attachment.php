<?php

// app/Models/Attachment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = ['path', 'mime', 'size', 'width', 'height'];

    public function attachable()
    {
        return $this->morphTo();
    }

    // helper
    public function url(): string
    {
        return Storage::url($this->path);
    }
}
