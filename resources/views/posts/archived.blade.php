<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-white p-2 rounded-xl hover:bg-gray-900 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-white">Archived Posts</h1>
            <p class="text-xs text-gray-500">Only visible to you</p>
        </div>
    </div>

    @forelse($posts as $post)
        <article class="bg-gray-900/30 border border-gray-800 rounded-2xl overflow-hidden mb-4 opacity-75 hover:opacity-100 transition">
            <div class="flex items-center justify-between p-4 pb-3">
                <div class="flex items-center gap-2.5">
                    <img src="{{ auth()->user()->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover" alt="">
                    <div>
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ $post->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <span class="text-[10px] px-2 py-1 rounded-full bg-gray-800 text-gray-400 font-semibold uppercase tracking-wider">Archived</span>
            </div>

            @if($post->media_path)
                <img src="{{ $post->media_url }}" class="w-full object-cover max-h-60" alt="">
            @endif

            @if($post->content)
                <p class="text-sm text-gray-300 px-4 py-3">{{ $post->content }}</p>
            @endif

            <div class="flex items-center gap-3 px-4 py-3 border-t border-gray-800 text-xs text-gray-500">
                <span>❤️ {{ $post->likes_count }}</span>
                <span>💬 {{ $post->comments_count }}</span>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('posts.show', $post->id) }}" class="text-violet-400 hover:underline">View</a>
                    <form action="{{ route('posts.archive', $post->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-cyan-400 hover:underline">Unarchive</button>
                    </form>
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                          onsubmit="return confirm('Delete this post permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="text-center py-20">
            <p class="text-4xl mb-3">📦</p>
            <p class="text-gray-400 font-semibold">No archived posts yet</p>
            <p class="text-gray-600 text-sm mt-1">Archive a post to hide it from your profile without deleting it.</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
</x-app-layout>
