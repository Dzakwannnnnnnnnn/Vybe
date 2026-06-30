<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Display the specified user profile.
     */
    public function show(User $user)
    {
        $user->loadCount(['followers', 'following']);

        // Load visual feed posts (media is typically present, type is feed)
        $feedPosts = $user->posts()
            ->where('type', 'feed')
            ->latest()
            ->get();

        // Load textual thread posts (excluding replies)
        $threadPosts = $user->posts()
            ->where('type', 'thread')
            ->whereNull('parent_id')
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'feedPosts', 'threadPosts'));
    }
}
