<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VoteController;
use App\Models\Board;

// ✅ route vote diletakkan DI SINI (sebelum fallback)
Route::middleware('anon')->post('/vote', [VoteController::class, 'store'])->name('vote.store');

Route::middleware('anon')->group(function () {
    // Home
    Route::get('/', function () {
        return view('home', ['boards' => Board::all()]);
    })->name('home');

    // Boards & Threads
    Route::get('/b/{board:slug}', [ThreadController::class, 'index'])->name('boards.show');
    Route::post('/b/{board:slug}/threads', [ThreadController::class, 'store'])->name('threads.store');

    // Thread detail
    Route::get('/t/{thread}', [ThreadController::class, 'show'])->name('threads.show');

    // Comments
    Route::post('/t/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Update (edit) & Delete (thread + comment)
    Route::patch('/t/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('/t/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');

    Route::patch('/c/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/c/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::fallback(function () {
    abort(404);
});
