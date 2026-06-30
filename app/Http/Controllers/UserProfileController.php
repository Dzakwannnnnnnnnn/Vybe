<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)
            ->withCount(['followers', 'following'])
            ->firstOrFail();

        $posts = $user->posts()
            ->visible()
            ->withCount(['likes', 'comments', 'reposts'])
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'posts'));
    }
}
