<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Posts — CRUD + interactions
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

    // Archived posts page
    Route::get('/archive', [PostController::class, 'archived'])->name('posts.archived');

    // Stories
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');

    // Follow System
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('follow.toggle');

    // User profiles
    Route::get('/users/{user:username}', [UserProfileController::class, 'show'])->name('users.show');

    // Profile settings (Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Direct Messages
    Route::get('/messages',                        [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{activeUser}',           [MessageController::class, 'index'])->name('messages.show');
    Route::post('/messages/{user}',                [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}/fetch',           [MessageController::class, 'fetchNew'])->name('messages.fetch');
});

require __DIR__.'/auth.php';
