<?php

namespace App\Policies;

use App\Models\{User, Thread};

class ThreadPolicy
{
    public function delete(?User $user, Thread $thread): bool
    {
        return $user && in_array($user->role, ['admin', 'moderator']);
    }
}
