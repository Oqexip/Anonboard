<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Models\Board;

/*
|--------------------------------------------------------------------------
| Public (Anonymous) + Anon middleware
|--------------------------------------------------------------------------
| Semua request melewati EnsureAnonSession agar ada anon_id, termasuk
| create/delete (controller yang memutuskan izin delete).
*/

Route::middleware('anon')->group(function () {

    // Home
    Route::get('/', function () {
        return view('home', ['boards' => Board::all()]);
    })->name('home');

    // Board & threads
    Route::get('/b/{board:slug}', [ThreadController::class, 'index'])->name('boards.show');
    Route::post('/b/{board:slug}/threads', [ThreadController::class, 'store'])->name('threads.store');

    // Thread detail
    Route::get('/t/{thread}', [ThreadController::class, 'show'])->name('threads.show');

    // Comments
    Route::post('/t/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Deletes (owner anon <=15 menit / owner user via policy / admin via Gate)
    Route::delete('/t/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');
    Route::delete('/c/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

/*
|--------------------------------------------------------------------------
| Authenticated (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
