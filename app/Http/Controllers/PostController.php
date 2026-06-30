<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // ── Create ───────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:feed,thread',
            'content' => 'nullable|string|max:1000',
            'media'   => 'nullable|image|max:10240',
        ]);

        if (!$request->content && !$request->hasFile('media')) {
            $error = 'Please write something or upload an image.';
            if ($request->ajax()) return response()->json(['error' => $error], 422);
            return redirect()->back()->withErrors(['content' => $error]);
        }

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id'    => Auth::id(),
            'type'       => $request->type,
            'content'    => $request->content,
            'media_path' => $mediaPath,
        ]);

        $post->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'post'    => $this->postPayload($post),
            ]);
        }

        return redirect()->back()->with('success', 'Post published!');
    }

    // ── Reply ────────────────────────────────────────────────────────────

    public function storeReply(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        Post::create([
            'user_id'   => Auth::id(),
            'type'      => 'thread',
            'content'   => $request->content,
            'parent_id' => $post->id,
        ]);

        return redirect()->back()->with('success', 'Reply posted!');
    }

    // ── Read ─────────────────────────────────────────────────────────────

    public function show(Post $post)
    {
        $post->load(['user', 'replies.user', 'replies.likes', 'comments.user', 'repostOf.user']);
        $post->loadCount(['likes', 'comments', 'reposts']);

        $ancestors = collect();
        $parent = $post->parent;
        while ($parent) {
            $ancestors->prepend($parent->load('user'));
            $parent = $parent->parent;
        }

        return view('posts.show', compact('post', 'ancestors'));
    }

    // ── Edit caption ─────────────────────────────────────────────────────

    public function edit(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);

        $request->validate(['content' => 'required|string|max:1000']);

        $post->update(['content' => $request->content]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'content' => $post->content]);
        }

        return redirect()->back()->with('success', 'Post updated!');
    }

    // ── Delete ───────────────────────────────────────────────────────────

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dashboard')->with('success', 'Post deleted.');
    }

    // ── Archive toggle ───────────────────────────────────────────────────

    public function archive(Post $post)
    {
        if ($post->user_id !== Auth::id()) abort(403);

        $post->update(['is_archived' => !$post->is_archived]);

        if (request()->ajax()) {
            return response()->json([
                'success'     => true,
                'is_archived' => $post->is_archived,
            ]);
        }

        return redirect()->back()->with('success', $post->is_archived ? 'Post archived.' : 'Post unarchived.');
    }

    // ── Repost (pure repost, no comment) ─────────────────────────────────

    public function repost(Post $post)
    {
        $userId = Auth::id();

        // Toggle: if already reposted, undo it
        $existing = Post::where('user_id', $userId)
            ->where('repost_of_id', $post->id)
            ->whereNull('content') // pure repost has no content
            ->first();

        if ($existing) {
            $existing->delete();
            $reposted = false;
        } else {
            Post::create([
                'user_id'      => $userId,
                'type'         => $post->type,
                'content'      => null,
                'repost_of_id' => $post->id,
            ]);
            $reposted = true;
        }

        $repostsCount = $post->reposts()->count();

        if (request()->ajax()) {
            return response()->json([
                'success'       => true,
                'reposted'      => $reposted,
                'reposts_count' => $repostsCount,
            ]);
        }

        return redirect()->back();
    }

    // ── Quote-repost (repost with comment, like Twitter) ──────────────────

    public function quoteRepost(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $newPost = Post::create([
            'user_id'      => Auth::id(),
            'type'         => $post->type,
            'content'      => $request->content,
            'repost_of_id' => $post->id,
        ]);

        $newPost->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'post'    => $this->postPayload($newPost),
            ]);
        }

        return redirect()->back()->with('success', 'Quote posted!');
    }

    // ── Like ─────────────────────────────────────────────────────────────

    public function like(Post $post)
    {
        $userId = Auth::id();
        $like   = Like::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $userId, 'post_id' => $post->id]);
            $liked = true;
        }

        if (request()->ajax()) {
            return response()->json(['liked' => $liked, 'likes_count' => $post->likes()->count()]);
        }

        return redirect()->back();
    }

    // ── Comment ──────────────────────────────────────────────────────────

    public function comment(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string|max:500']);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'content' => $request->content,
        ]);

        $comment->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'content'  => $comment->content,
                    'username' => $comment->user->username,
                    'name'     => $comment->user->name,
                    'avatar'   => $comment->user->profile_picture_url,
                ],
                'comments_count' => $post->comments()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Comment added!');
    }

    // ── Archived posts page ───────────────────────────────────────────────

    public function archived()
    {
        $posts = Post::where('user_id', Auth::id())
            ->archived()
            ->with(['user'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('posts.archived', compact('posts'));
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function postPayload(Post $post): array
    {
        $post->loadCount(['likes', 'comments', 'reposts']);
        return [
            'id'               => $post->id,
            'type'             => $post->type,
            'content'          => $post->content,
            'media_url'        => $post->media_url,
            'likes_count'      => $post->likes_count,
            'comments_count'   => $post->comments_count,
            'reposts_count'    => $post->reposts_count,
            'is_liked'         => false,
            'is_reposted'      => false,
            'repost_of'        => null,
            'created_at_human' => $post->created_at->diffForHumans(),
            'show_url'         => route('posts.show', $post->id),
            'like_url'         => route('posts.like', $post->id),
            'comment_url'      => route('posts.comment', $post->id),
            'reply_url'        => route('posts.reply', $post->id),
            'edit_url'         => route('posts.edit', $post->id),
            'delete_url'       => route('posts.destroy', $post->id),
            'archive_url'      => route('posts.archive', $post->id),
            'repost_url'       => route('posts.repost', $post->id),
            'quote_url'        => route('posts.quote', $post->id),
            'user' => [
                'name'                => $post->user->name,
                'username'            => $post->user->username,
                'profile_picture_url' => $post->user->profile_picture_url,
                'profile_url'         => route('users.show', $post->user->username),
            ],
        ];
    }
}
