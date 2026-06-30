<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Follow or unfollow a user.
     */
    public function toggle(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot follow yourself.');
        }

        // Toggle relationship
        if ($currentUser->isFollowing($user)) {
            $currentUser->following()->detach($user->id);
            $message = 'Unfollowed ' . $user->name;
        } else {
            $currentUser->following()->attach($user->id);
            $message = 'Following ' . $user->name;
        }

        return redirect()->back()->with('success', $message);
    }
}
