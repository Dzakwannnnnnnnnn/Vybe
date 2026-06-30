<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    /**
     * Store a new story.
     */
    public function store(Request $request)
    {
        $request->validate([
            'media' => 'required|image|max:10240', // Max 10MB
        ]);

        $mediaPath = $request->file('media')->store('stories', 'public');

        Story::create([
            'user_id' => Auth::id(),
            'media_path' => $mediaPath,
            'expires_at' => now()->addHours(24),
        ]);

        return redirect()->back()->with('success', 'Story uploaded! It will disappear in 24 hours.');
    }
}
