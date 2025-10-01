<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EnsureAnonKey
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::check()) {
            if (!$request->session()->has('anon_key')) {
                $request->session()->put('anon_key', 'anon_' . Str::uuid()->toString());
            }
        }

        return $next($request);
    }
}
