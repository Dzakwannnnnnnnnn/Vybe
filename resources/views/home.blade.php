<x-app-layout>
<div class="max-w-7xl mx-auto flex" x-data="vybeHome()">

    {{-- ════════════════════════════ MAIN FEED ════════════════════════════ --}}
    <div class="flex-1 max-w-2xl border-r border-gray-800 min-h-screen">

        {{-- ── Story Tray ────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-4 overflow-x-auto py-4 px-4 border-b border-gray-800 no-scrollbar">

            {{-- Own story / upload button --}}
            <div class="flex flex-col items-center flex-shrink-0 cursor-pointer" @click="storyUploadOpen = true">
                <div class="relative w-16 h-16 rounded-full border-2 border-dashed border-gray-700 flex items-center justify-center hover:border-violet-500 transition group">
                    <img src="{{ auth()->user()->profile_picture_url }}" class="w-14 h-14 rounded-full object-cover opacity-80 group-hover:opacity-60 transition" alt="Me">
                    <span class="absolute bottom-0 right-0 bg-violet-600 rounded-full w-5 h-5 flex items-center justify-center text-white text-xs font-bold border-2 border-gray-950">+</span>
                </div>
                <span class="text-[11px] text-gray-500 mt-1.5">Your Story</span>
            </div>

            {{-- Active stories --}}
            @forelse($activeStories as $userId => $userStories)
                @php $su = $userStories->first()->user; @endphp
                <div class="flex flex-col items-center flex-shrink-0 cursor-pointer"
                     @click="openStory({{ json_encode($userStories->map(fn($s)=>$s->media_url)->values()) }}, '{{ addslashes($su->name) }}', '{{ $su->profile_picture_url }}')">
                    <div class="w-16 h-16 rounded-full p-[2px] bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600">
                        <img src="{{ $su->profile_picture_url }}" class="w-full h-full rounded-full object-cover border-2 border-gray-950" alt="{{ $su->name }}">
                    </div>
                    <span class="text-[11px] text-gray-400 mt-1.5 truncate max-w-[64px]">{{ $su->name }}</span>
                </div>
            @empty
            @endforelse
        </div>

        {{-- ── Story Upload Modal ────────────────────────────────────────── --}}
        <div x-show="storyUploadOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" x-cloak @keydown.escape.window="storyUploadOpen=false">
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 max-w-sm w-full shadow-2xl" @click.away="storyUploadOpen=false">
                <h3 class="text-base font-bold text-white mb-4">Add Story</h3>
                <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block border-2 border-dashed border-gray-700 hover:border-violet-500 transition rounded-xl p-8 text-center cursor-pointer relative mb-4">
                        <input type="file" name="media" required class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                               x-ref="storyFile" @change="storyPreviewUrl = URL.createObjectURL($event.target.files[0])">
                        <template x-if="!storyPreviewUrl">
                            <div class="text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-sm font-medium">Click to pick image</p>
                                <p class="text-xs text-gray-600 mt-1">Disappears after 24h</p>
                            </div>
                        </template>
                        <template x-if="storyPreviewUrl">
                            <img :src="storyPreviewUrl" class="max-h-44 mx-auto rounded-lg object-contain">
                        </template>
                    </label>
                    <div class="flex gap-3">
                        <button type="button" class="flex-1 py-2 rounded-xl text-sm font-semibold bg-gray-800 hover:bg-gray-700 transition" @click="storyUploadOpen=false;storyPreviewUrl=null">Cancel</button>
                        <button type="submit" class="flex-1 py-2 rounded-xl text-sm font-bold bg-gradient-to-r from-violet-500 to-pink-500 hover:opacity-90 transition">Share Story</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Post Creator ──────────────────────────────────────────────── --}}
        <div class="border-b border-gray-800 px-4 py-5">
            {{-- Tab switcher --}}
            <div class="flex mb-4 gap-1 bg-gray-900/60 rounded-xl p-1">
                <button class="flex-1 py-2 rounded-lg text-xs font-bold transition-all"
                        :class="postType==='thread' ? 'bg-gray-800 text-white shadow' : 'text-gray-500 hover:text-gray-300'"
                        @click="postType='thread'; mediaPreview=null">
                    💬 Thread
                </button>
                <button class="flex-1 py-2 rounded-lg text-xs font-bold transition-all"
                        :class="postType==='feed' ? 'bg-gray-800 text-white shadow' : 'text-gray-500 hover:text-gray-300'"
                        @click="postType='feed'">
                    🖼️ Feed Post
                </button>
            </div>

            {{-- Input row --}}
            <div class="flex gap-3">
                <img src="{{ auth()->user()->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 mt-1" alt="Me">
                <div class="flex-1 min-w-0">
                    <textarea x-model="postContent" :placeholder="postType==='thread' ? 'What\'s on your mind?' : 'Write a caption…'"
                              class="w-full bg-transparent border-0 focus:ring-0 text-white placeholder-gray-600 resize-none text-sm leading-relaxed no-scrollbar"
                              rows="3" maxlength="1000"></textarea>

                    {{-- Media preview --}}
                    <div x-show="mediaPreview" class="relative mt-2 rounded-xl overflow-hidden border border-gray-800" x-cloak>
                        <img :src="mediaPreview" class="max-h-60 w-full object-cover">
                        <button type="button" @click="mediaPreview=null; $refs.mediaInput.value=''"
                                class="absolute top-2 right-2 bg-black/70 hover:bg-black p-1.5 rounded-full text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Actions row --}}
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-800/60">
                        <label class="cursor-pointer text-gray-500 hover:text-violet-400 p-1.5 rounded-lg hover:bg-gray-900 transition"
                               :class="postType==='thread' ? 'opacity-30 pointer-events-none' : ''">
                            <input type="file" class="hidden" x-ref="mediaInput" accept="image/*"
                                   @change="mediaPreview = URL.createObjectURL($event.target.files[0])">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </label>

                        <div class="flex items-center gap-3">
                            {{-- Character counter --}}
                            <span class="text-xs font-mono" :class="postContent.length > 950 ? 'text-red-400' : 'text-gray-600'" x-text="postContent.length + '/1000'"></span>
                            <button @click="submitPost()" :disabled="posting"
                                    class="px-5 py-1.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white text-xs font-bold rounded-full transition flex items-center gap-1.5">
                                <span x-show="posting" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <span x-text="posting ? 'Posting…' : 'Post'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Error message --}}
                    <p x-show="postError" x-text="postError" class="text-xs text-red-400 mt-2" x-cloak></p>
                </div>
            </div>
        </div>

        {{-- ── Timeline Tabs ─────────────────────────────────────────────── --}}
        <div class="flex border-b border-gray-800 sticky top-0 bg-gray-950/90 backdrop-blur z-10">
            <button class="flex-1 py-3.5 text-xs font-bold border-b-2 transition"
                    :class="timelineTab==='feed' ? 'border-violet-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300'"
                    @click="timelineTab='feed'">
                Feed
            </button>
            <button class="flex-1 py-3.5 text-xs font-bold border-b-2 transition"
                    :class="timelineTab==='thread' ? 'border-violet-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300'"
                    @click="timelineTab='thread'">
                Threads
            </button>
        </div>

        {{-- ── FEED Posts ────────────────────────────────────────────────── --}}
        <div x-show="timelineTab==='feed'" class="divide-y divide-gray-800/50">

            {{-- Realtime-prepended feed posts rendered by Alpine --}}
            <template x-for="post in feedPosts" :key="post.id">
                <article class="py-4 px-4">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <a :href="post.user.profile_url" class="flex items-center gap-2.5">
                            <img :src="post.user.profile_picture_url" class="w-9 h-9 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-semibold text-white leading-none" x-text="post.user.name"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="'@ ' + post.user.username"></p>
                            </div>
                        </a>
                        <span class="text-xs text-gray-600" x-text="post.created_at_human"></span>
                    </div>

                    {{-- Media --}}
                    <template x-if="post.media_url">
                        <div class="rounded-xl overflow-hidden border border-gray-800 mb-3">
                            <img :src="post.media_url" class="w-full object-cover max-h-[500px]" alt="Post image">
                        </div>
                    </template>

                    {{-- Caption --}}
                    <template x-if="post.content">
                        <p class="text-sm text-gray-200 mb-3 leading-relaxed" x-text="post.content"></p>
                    </template>

                    {{-- Actions --}}
                    <div class="flex items-center gap-5">
                        <button @click="toggleLike(post)" class="flex items-center gap-1.5 transition" :class="post.is_liked ? 'text-pink-500' : 'text-gray-500 hover:text-pink-400'">
                            <svg class="w-5 h-5" :fill="post.is_liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <span class="text-xs font-semibold" x-text="post.likes_count"></span>
                        </button>
                        <button @click="post.commentOpen = !post.commentOpen" class="flex items-center gap-1.5 text-gray-500 hover:text-violet-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span class="text-xs font-semibold" x-text="post.comments_count"></span>
                        </button>
                    </div>

                    {{-- Comment box --}}
                    <div x-show="post.commentOpen" class="mt-3 pt-3 border-t border-gray-800/60" x-cloak>
                        <div class="flex gap-2 mb-2">
                            <input x-model="post.commentInput" type="text" placeholder="Add a comment…"
                                   class="flex-1 bg-gray-900 border border-gray-800 rounded-full px-4 py-1.5 text-xs text-white focus:outline-none focus:border-violet-500"
                                   @keydown.enter.prevent="submitComment(post)">
                            <button @click="submitComment(post)" class="bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold px-4 py-1.5 rounded-full transition">Send</button>
                        </div>
                        <div class="space-y-2 max-h-36 overflow-y-auto no-scrollbar">
                            <template x-for="c in (post.comments ?? [])" :key="c.username + c.content">
                                <div class="flex gap-2 items-start">
                                    <img :src="c.avatar" class="w-6 h-6 rounded-full object-cover flex-shrink-0 mt-0.5" alt="">
                                    <p class="text-xs text-gray-300 leading-relaxed">
                                        <strong class="text-white mr-1" x-text="'@' + c.username"></strong>
                                        <span x-text="c.content"></span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </article>
            </template>

            {{-- Server-side paginated feed posts (existing) --}}
            @foreach($posts->where('type','feed') as $post)
                <article class="py-4 px-4" x-data="postCard({{ $post->id }}, {{ $post->isLikedBy(auth()->user()) ? 'true':'false' }}, {{ $post->likes_count }}, {{ $post->comments_count }})">
                    <div class="flex items-center justify-between mb-3">
                        <a href="{{ route('users.show', $post->user->username) }}" class="flex items-center gap-2.5">
                            <img src="{{ $post->user->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-semibold text-white leading-none">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">@&nbsp;{{ $post->user->username }}</p>
                            </div>
                        </a>
                        <span class="text-xs text-gray-600">{{ $post->created_at->diffForHumans() }}</span>
                    </div>

                    @if($post->media_path)
                        <div class="rounded-xl overflow-hidden border border-gray-800 mb-3">
                            <img src="{{ $post->media_url }}" class="w-full object-cover max-h-[500px]" alt="Post image">
                        </div>
                    @endif

                    @if($post->content)
                        <p class="text-sm text-gray-200 mb-3 leading-relaxed">{{ $post->content }}</p>
                    @endif

                    <div class="flex items-center gap-5">
                        <button @click="toggleLike('{{ route('posts.like', $post->id) }}')" class="flex items-center gap-1.5 transition" :class="liked ? 'text-pink-500' : 'text-gray-500 hover:text-pink-400'">
                            <svg class="w-5 h-5" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <span class="text-xs font-semibold" x-text="likesCount"></span>
                        </button>
                        <button @click="commentOpen=!commentOpen" class="flex items-center gap-1.5 text-gray-500 hover:text-violet-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span class="text-xs font-semibold" x-text="commentsCount"></span>
                        </button>
                    </div>

                    <div x-show="commentOpen" class="mt-3 pt-3 border-t border-gray-800/60" x-cloak>
                        <div class="flex gap-2 mb-2">
                            <input x-model="commentInput" type="text" placeholder="Add a comment…"
                                   class="flex-1 bg-gray-900 border border-gray-800 rounded-full px-4 py-1.5 text-xs text-white focus:outline-none focus:border-violet-500"
                                   @keydown.enter.prevent="submitComment('{{ route('posts.comment', $post->id) }}')">
                            <button @click="submitComment('{{ route('posts.comment', $post->id) }}')" class="bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold px-4 py-1.5 rounded-full transition">Send</button>
                        </div>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto no-scrollbar">
                            @foreach($post->comments as $c)
                                <p class="text-xs text-gray-300 comment-item">
                                    <strong class="text-white mr-1">@{{ $c->user->username }}</strong> {{ $c->content }}
                                </p>
                            @endforeach
                            <template x-for="c in newComments" :key="c.username + c.content">
                                <div class="flex gap-2 items-start">
                                    <img :src="c.avatar" class="w-6 h-6 rounded-full object-cover flex-shrink-0 mt-0.5" alt="">
                                    <p class="text-xs text-gray-300 leading-relaxed">
                                        <strong class="text-white mr-1" x-text="'@' + c.username"></strong>
                                        <span x-text="c.content"></span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </article>
            @endforeach

            @if($posts->where('type','feed')->isEmpty() && count($feedPosts ?? []) === 0)
                <div id="feed-empty" class="text-center py-16 px-4">
                    <p class="text-4xl mb-3">🖼️</p>
                    <p class="text-gray-400 font-semibold">No feed posts yet</p>
                    <p class="text-gray-600 text-sm mt-1">Be the first to share something visual!</p>
                </div>
            @endif
        </div>

        {{-- ── THREAD Posts ──────────────────────────────────────────────── --}}
        <div x-show="timelineTab==='thread'" class="divide-y divide-gray-800/50">

            {{-- Realtime-prepended threads --}}
            <template x-for="post in threadPosts" :key="post.id">
                <article class="px-4 py-4 hover:bg-gray-900/30 transition cursor-pointer" @click="window.location=post.show_url">
                    <div class="flex gap-3">
                        <a :href="post.user.profile_url" class="flex-shrink-0" @click.stop>
                            <img :src="post.user.profile_picture_url" class="w-10 h-10 rounded-full object-cover" alt="">
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <a :href="post.user.profile_url" class="text-sm font-semibold text-white hover:underline truncate" @click.stop x-text="post.user.name"></a>
                                    <span class="text-xs text-gray-500 truncate" x-text="'@ ' + post.user.username"></span>
                                </div>
                                <span class="text-xs text-gray-600 flex-shrink-0 ml-2" x-text="post.created_at_human"></span>
                            </div>
                            <p class="text-sm text-gray-200 mt-1.5 leading-relaxed whitespace-pre-line" x-text="post.content"></p>
                            <div class="flex items-center gap-5 mt-3">
                                <button @click.stop="toggleLike(post)" class="flex items-center gap-1.5 transition" :class="post.is_liked ? 'text-pink-500' : 'text-gray-500 hover:text-pink-400'">
                                    <svg class="w-4 h-4" :fill="post.is_liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span class="text-xs" x-text="post.likes_count"></span>
                                </button>
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span class="text-xs" x-text="post.comments_count"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </template>

            {{-- Server-side paginated threads --}}
            @foreach($posts->where('type','thread') as $post)
                <article class="px-4 py-4 hover:bg-gray-900/30 transition cursor-pointer"
                         onclick="window.location='{{ route('posts.show', $post->id) }}'"
                         x-data="postCard({{ $post->id }}, {{ $post->isLikedBy(auth()->user()) ? 'true':'false' }}, {{ $post->likes_count }}, {{ $post->comments_count }})">
                    <div class="flex gap-3">
                        <a href="{{ route('users.show', $post->user->username) }}" class="flex-shrink-0" onclick="event.stopPropagation()">
                            <img src="{{ $post->user->profile_picture_url }}" class="w-10 h-10 rounded-full object-cover" alt="">
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <a href="{{ route('users.show', $post->user->username) }}" class="text-sm font-semibold text-white hover:underline truncate" onclick="event.stopPropagation()">{{ $post->user->name }}</a>
                                    <span class="text-xs text-gray-500">@&nbsp;{{ $post->user->username }}</span>
                                </div>
                                <span class="text-xs text-gray-600 flex-shrink-0 ml-2">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-200 mt-1.5 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                            <div class="flex items-center gap-5 mt-3">
                                <button @click.stop="toggleLike('{{ route('posts.like', $post->id) }}')" class="flex items-center gap-1.5 transition" :class="liked ? 'text-pink-500' : 'text-gray-500 hover:text-pink-400'">
                                    <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span class="text-xs" x-text="likesCount"></span>
                                </button>
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span class="text-xs" x-text="commentsCount"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach

            @if($posts->where('type','thread')->isEmpty())
                <div id="thread-empty" class="text-center py-16 px-4" x-show="threadPosts.length === 0">
                    <p class="text-4xl mb-3">💬</p>
                    <p class="text-gray-400 font-semibold">No threads yet</p>
                    <p class="text-gray-600 text-sm mt-1">Start a conversation!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════ RIGHT SIDEBAR ════════════════════════════════ --}}
    <aside class="hidden lg:block w-80 px-6 py-6 space-y-6 flex-shrink-0">
        {{-- Search --}}
        <div class="relative">
            <input type="text" placeholder="Search Vybe…"
                   class="w-full bg-gray-900 border border-gray-800 rounded-2xl py-2.5 pl-11 pr-4 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
            <svg class="w-5 h-5 absolute left-3.5 top-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        {{-- Who to follow --}}
        @if($recommendations->count())
        <div class="bg-gray-900/40 border border-gray-800 rounded-2xl p-4">
            <h3 class="text-sm font-bold text-white mb-4">Who to follow</h3>
            <div class="space-y-4">
                @foreach($recommendations as $rec)
                    <div class="flex items-center justify-between">
                        <a href="{{ $rec->username ? route('users.show', $rec->username) : '#' }}" class="flex items-center gap-2.5 min-w-0">
                            <img src="{{ $rec->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="">
                            <div class="truncate">
                                <p class="text-xs font-semibold text-white truncate hover:underline">{{ $rec->name }}</p>
                                <p class="text-[10px] text-gray-500">@&nbsp;{{ $rec->username ?? '—' }}</p>
                            </div>
                        </a>
                        <form action="{{ route('follow.toggle', $rec->id) }}" method="POST" class="ml-2">
                            @csrf
                            <button type="submit" class="bg-white hover:bg-gray-100 text-black text-[11px] font-bold py-1.5 px-3.5 rounded-full transition flex-shrink-0">Follow</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <p class="text-[10px] text-gray-700 px-1">&copy; 2026 Vybe · Built for sharing vibes.</p>
    </aside>

    {{-- ════════════════════ STORY VIEWER MODAL ════════════════════════════ --}}
    <div x-show="storyOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/95"
         x-cloak @keydown.escape.window="closeStory()">
        <div class="absolute inset-0" @click="closeStory()"></div>
        <div class="relative w-full max-w-sm h-[88vh] bg-gray-950 rounded-2xl overflow-hidden border border-gray-800 z-10 flex flex-col">
            {{-- Progress bars --}}
            <div class="absolute top-2 left-2 right-2 flex gap-1 z-20">
                <template x-for="(s, idx) in storySlides" :key="idx">
                    <div class="flex-1 h-[3px] bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-none"
                             :style="idx === storyIdx ? 'width:' + storyProgress + '%' : (idx < storyIdx ? 'width:100%' : 'width:0%')"></div>
                    </div>
                </template>
            </div>

            {{-- Top meta --}}
            <div class="absolute top-6 left-3 right-3 flex items-center justify-between z-20">
                <div class="flex items-center gap-2">
                    <img :src="storyAuthor.avatar" class="w-8 h-8 rounded-full object-cover border border-pink-400">
                    <span class="text-sm font-semibold text-white drop-shadow" x-text="storyAuthor.name"></span>
                </div>
                <button @click="closeStory()" class="text-white bg-black/50 p-1.5 rounded-full hover:bg-black/80">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Tap areas --}}
            <div class="absolute left-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer" @click="prevStory()"></div>
            <div class="absolute right-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer" @click="nextStory()"></div>

            {{-- Image --}}
            <div class="flex-1 flex items-center justify-center bg-black p-2">
                <img :src="storySlides[storyIdx]" class="max-h-full max-w-full object-contain rounded-lg shadow-2xl" alt="Story">
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════ Alpine.js Logic ══════════════════════════ --}}
<script>
const CSRF = '{{ csrf_token() }}';
const POST_STORE_URL = '{{ route('posts.store') }}';
const MY_AVATAR = '{{ auth()->user()->profile_picture_url }}';
const MY_NAME   = '{{ auth()->user()->name }}';
const MY_USER   = '{{ auth()->user()->username }}';
const MY_PROFILE_URL = '{{ auth()->user()->username ? route('users.show', auth()->user()->username) : '#' }}';

function vybeHome() {
    return {
        // UI state
        timelineTab: 'feed',
        storyUploadOpen: false,
        storyPreviewUrl: null,

        // Post creator
        postType: 'thread',
        postContent: '',
        mediaPreview: null,
        posting: false,
        postError: null,

        // Real-time post lists (prepended)
        feedPosts: [],
        threadPosts: [],

        // Story viewer
        storyOpen: false,
        storySlides: [],
        storyAuthor: {},
        storyIdx: 0,
        storyProgress: 0,
        _storyTimer: null,

        // ── Posting ────────────────────────────────────────────────────
        async submitPost() {
            this.postError = null;
            const content = this.postContent.trim();
            const file = this.$refs.mediaInput?.files?.[0];
            if (!content && !file) {
                this.postError = 'Write something or pick an image!';
                return;
            }
            if (this.postType === 'feed' && !file) {
                this.postError = 'Feed posts need an image!';
                return;
            }

            this.posting = true;
            const form = new FormData();
            form.append('_token', CSRF);
            form.append('type', this.postType);
            form.append('content', content);
            if (file) form.append('media', file);

            try {
                const res  = await fetch(POST_STORE_URL, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    this.postError = data.error ?? 'Something went wrong.';
                    return;
                }
                const p = { ...data.post, commentOpen: false, commentInput: '', comments: [] };
                if (this.postType === 'feed') {
                    this.feedPosts.unshift(p);
                    this.timelineTab = 'feed';
                    // hide empty placeholder
                    const el = document.getElementById('feed-empty');
                    if (el) el.style.display = 'none';
                } else {
                    this.threadPosts.unshift(p);
                    this.timelineTab = 'thread';
                    const el = document.getElementById('thread-empty');
                    if (el) el.style.display = 'none';
                }
                // Reset form
                this.postContent = '';
                this.mediaPreview = null;
                if (this.$refs.mediaInput) this.$refs.mediaInput.value = '';
            } catch (e) {
                this.postError = 'Network error, please try again.';
            } finally {
                this.posting = false;
            }
        },

        // ── Like (for realtime posts) ──────────────────────────────────
        async toggleLike(post) {
            const res  = await fetch(post.like_url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            post.is_liked    = data.liked;
            post.likes_count = data.likes_count;
        },

        // ── Comment (for realtime posts) ───────────────────────────────
        async submitComment(post) {
            const content = post.commentInput?.trim();
            if (!content) return;
            const form = new FormData();
            form.append('_token', CSRF);
            form.append('content', content);
            const res  = await fetch(post.comment_url, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (data.success) {
                if (!post.comments) post.comments = [];
                post.comments.push(data.comment);
                post.comments_count = data.comments_count;
                post.commentInput   = '';
            }
        },

        // ── Story viewer ───────────────────────────────────────────────
        openStory(slides, name, avatar) {
            this.storySlides  = slides;
            this.storyAuthor  = { name, avatar };
            this.storyIdx     = 0;
            this.storyOpen    = true;
            this.storyProgress= 0;
            this._startTimer();
        },
        _startTimer() {
            if (this._storyTimer) clearInterval(this._storyTimer);
            this.storyProgress = 0;
            let step = 0;
            const STEPS = 100; // 4s / 40ms
            this._storyTimer = setInterval(() => {
                step++;
                this.storyProgress = step;
                if (step >= STEPS) this.nextStory();
            }, 40);
        },
        nextStory() {
            if (this.storyIdx < this.storySlides.length - 1) { this.storyIdx++; this._startTimer(); }
            else this.closeStory();
        },
        prevStory() {
            if (this.storyIdx > 0) { this.storyIdx--; this._startTimer(); }
        },
        closeStory() {
            this.storyOpen = false;
            if (this._storyTimer) clearInterval(this._storyTimer);
        },
    };
}

// Alpine component for each server-rendered post card (like + comment)
function postCard(postId, isLiked, likesCount, commentsCount) {
    return {
        liked: isLiked,
        likesCount,
        commentsCount,
        commentOpen: false,
        commentInput: '',
        newComments: [],

        async toggleLike(url) {
            const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            this.liked      = data.liked;
            this.likesCount = data.likes_count;
        },

        async submitComment(url) {
            const content = this.commentInput.trim();
            if (!content) return;
            const form = new FormData();
            form.append('_token', CSRF);
            form.append('content', content);
            const res  = await fetch(url, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (data.success) {
                this.newComments.push({
                    username: data.comment.username,
                    name:     data.comment.name,
                    avatar:   data.comment.avatar,
                    content:  data.comment.content,
                });
                this.commentsCount = data.comments_count;
                this.commentInput  = '';
            }
        },
    };
}
</script>
</x-app-layout>
