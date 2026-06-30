<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard / Beranda
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // 2. Archived Posts Page (Harus di atas route posts/{post} agar tidak dikira ID '{post}')
    Route::get('/posts/archive-list', [PostController::class, 'archived'])->name('posts.archived');

    // 3. Posts — CRUD + Interactions
    Route::post('/posts',                    [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}',              [PostController::class, 'show'])->name('posts.show');
    Route::patch('/posts/{post}',            [PostController::class, 'edit'])->name('posts.edit');
    Route::delete('/posts/{post}',           [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/reply',       [PostController::class, 'storeReply'])->name('posts.reply');
    Route::post('/posts/{post}/like',        [PostController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/comment',     [PostController::class, 'comment'])->name('posts.comment');
    Route::post('/posts/{post}/archive',     [PostController::class, 'archive'])->name('posts.archive');
    Route::post('/posts/{post}/repost',      [PostController::class, 'repost'])->name('posts.repost');
    Route::post('/posts/{post}/quote',       [PostController::class, 'quoteRepost'])->name('posts.quote');

    // 4. Stories
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');

    // 5. Profile Settings (Laravel Breeze Default)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 6. Direct Messages
    Route::get('/messages',                        [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{activeUser}',           [MessageController::class, 'index'])->name('messages.show');
    Route::post('/messages/{user}',                [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}/fetch',           [MessageController::class, 'fetchNew'])->name('messages.fetch');

    // 7. Follow System & User Profiles
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('follow.toggle');
    
    // PERBAIKAN DI SINI: Diubah menjadi {username} agar sinkron dengan Controller & Blade
    Route::get('/users/{username}', [UserProfileController::class, 'show'])->name('users.show');
});

require __DIR__.'/auth.php';