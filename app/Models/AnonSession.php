<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnonSession extends Model
{
    protected $fillable = [
        'session_hash',
        'ip_hash',
        'ua_hash',
        'blocked_until',
    ];
}
