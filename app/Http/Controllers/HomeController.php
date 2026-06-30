<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Get users we follow
        $followingIds = $user->following()->pluck('users.id')->toArray();
        $followedAndSelfIds = array_merge($followingIds, [$user->id]);

        // 2. Get active stories from these users
        // Group by user so we can display them nicely in the story tray
        $activeStories = Story::active()
            ->whereIn('user_id', $followedAndSelfIds)
            ->with('user')
            ->get()
            ->groupBy('user_id');

        // 3. Get posts (feed & threads) for timeline, excluding direct replies (parent_id = null)
        // Optionally, we can show posts from followed users + self, or all global posts.
        // Let's do followed users + self for feed, but also allow a global/explore option if desired.
        // For a livelier demo, let's load posts from everyone (global) but prioritize or just load all main posts.
        // Let's load posts from followed users + self.
        $posts = Post::whereIn('user_id', $followedAndSelfIds)
            ->whereNull('parent_id')
            ->visible()
            ->with(['user', 'likes', 'comments.user', 'repostOf.user'])
            ->withCount(['likes', 'comments', 'reposts'])
            ->latest()
            ->paginate(10);

        // 4. Recommend users to follow (users we aren't following yet, excluding self)
        $recommendations = User::whereNotIn('id', $followedAndSelfIds)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('home', compact('posts', 'activeStories', 'recommendations'));
    }
}
