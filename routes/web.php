<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureAnonSession;
use App\Http\Controllers\{ThreadController, CommentController, VoteController, ReportController};
use App\Models\Board;


// Rate limiters
RateLimiter::for('post-actions', function (Request $request) {
    $key = ($request->session()->getId() ?? 'guest') . '|' . $request->ip();
    return [\Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($key)];
});


// Home
Route::get('/', function () {
    $boards = Board::all();
    return view('home', compact('boards'));
});


Route::middleware(['web', EnsureAnonSession::class])->group(function () {
    Route::get('/b/{board:slug}', [ThreadController::class, 'index'])->name('boards.show');
    Route::get('/t/{thread}', [ThreadController::class, 'show'])->name('threads.show');


    Route::post('/b/{board:slug}/threads', [ThreadController::class, 'store'])->middleware('throttle:post-actions');
    Route::post('/t/{thread}/comments', [CommentController::class, 'store'])->middleware('throttle:post-actions');


    Route::post('/vote', [VoteController::class, 'store'])->middleware('throttle:post-actions');
    Route::post('/report', [ReportController::class, 'store'])->middleware('throttle:post-actions');
});

Route::post('/upload', function (Request $request) {
    $request->validate(['image' => ['required', 'image', 'max:2048']]);
    $path = $request->file('image')->store('public/uploads');
    return ['url' => \Illuminate\Support\Facades\Storage::url($path)];
})->middleware(['web', EnsureAnonSession::class, 'throttle:post-actions']);