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
        // Daftarkan mapping policy (aman di semua versi)
        $this->registerPolicies();

        // Admin full access
        Gate::before(function (?User $user) {
            return $user && $user->isAdmin() ? true : null;
        });
    }
}
