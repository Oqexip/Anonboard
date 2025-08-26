<?php

namespace App\Policies;

use App\Models\{User, Thread};

class ThreadPolicy
{
    public function delete(User $user, Thread $thread): bool
    {
        return $user->isAdmin() || $thread->user_id === $user->id;
    }

    public function create(?User $user): bool
    {
        // semua boleh (login maupun anon). Policy ini hanya untuk contoh.
        return true;
    }
}
