<?php

namespace app\Http\Middleware;

use app\Models\AnonSession;
use Closure;
use Illuminate\Http\Request;

class EnsureAnonSession
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil ID session Laravel lalu hash dengan APP_KEY (tidak menyimpan IP mentah)
        $sess = $request->session()->getId();
        $sessionHash = hash_hmac('sha256', $sess, config('app.key'));

        $ipHash = $request->ip()
            ? hash('sha256', $request->ip() . config('app.key'))
            : null;

        $uaHash = $request->userAgent()
            ? hash('sha256', $request->userAgent() . config('app.key'))
            : null;

        $anon = AnonSession::firstOrCreate(
            ['session_hash' => $sessionHash],
            ['ip_hash' => $ipHash, 'ua_hash' => $uaHash]
        );

        if ($anon->blocked_until && now()->lessThan($anon->blocked_until)) {
            abort(429, 'You are temporarily blocked due to abuse.');
        }

        // simpan anon_id ke request agar bisa dipakai controller/view
        $request->attributes->set('anon_id', $anon->id);

        return $next($request);
    }
}
