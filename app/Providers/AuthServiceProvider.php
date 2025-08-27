<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{Thread, Comment, User};
use App\Policies\{ThreadPolicy, CommentPolicy};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Thread::class  => ThreadPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    public function boot(): void
    {
        Gate::before(function (?User $user, string $ability) {
            // admin tetap full access KECUALI untuk update
            if ($ability === 'update') {
                return null;
            }
            return $user && $user->isAdmin() ? true : null;
        });
    }
}
