<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-6 min-h-screen">

    {{-- Back --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-white p-2 rounded-xl hover:bg-gray-900 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-base font-bold text-white">{{ $post->type === 'thread' ? 'Thread' : 'Post' }}</h1>
    </div>

    {{-- Ancestor chain --}}
    @foreach($ancestors as $ancestor)
        <div class="flex gap-3 mb-1 opacity-60">
            <div class="flex flex-col items-center">
                <img src="{{ $ancestor->user->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="">
                <div class="flex-1 w-[2px] bg-gray-800 my-1 min-h-[16px]"></div>
            </div>
            <div class="pb-3 flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                    <a href="{{ route('users.show', $ancestor->user->username) }}" class="text-sm font-semibold text-gray-300 hover:underline">{{ $ancestor->user->name }}</a>
                    <span class="text-xs text-gray-600">· {{ $ancestor->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed whitespace-pre-line">{{ $ancestor->content }}</p>
            </div>
        </div>
    @endforeach

    {{-- ── Main Post ───────────────────────────────────────────────────── --}}
    <div class="bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden mb-5"
         x-data="postDetail(
            {{ $post->id }},
            {{ $post->isLikedBy(auth()->user()) ? 'true':'false' }},
            {{ $post->likes_count }},
            {{ $post->comments_count }},
            {{ $post->reposts_count }},
            {{ $post->isRepostedBy(auth()->user()) ? 'true':'false' }},
            {{ auth()->id() === $post->user_id ? 'true':'false' }}
         )">

        {{-- Header --}}
        <div class="flex items-center justify-between p-4 pb-3">
            <a href="{{ route('users.show', $post->user->username) }}" class="flex items-center gap-3">
                <img src="{{ $post->user->profile_picture_url }}" class="w-11 h-11 rounded-full object-cover" alt="">
                <div>
                    <p class="text-sm font-bold text-white leading-none">{{ $post->user->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">@&nbsp;{{ $post->user->username }}</p>
                </div>
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-600">{{ $post->created_at->format('M d, Y · g:i A') }}</span>

                {{-- Owner actions dropdown --}}
                @if(auth()->id() === $post->user_id)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open=!open" class="p-1.5 rounded-lg hover:bg-gray-800 text-gray-500 hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        </button>
                        <div x-show="open" @click.away="open=false"
                             class="absolute right-0 top-8 w-44 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl z-20 py-1 text-sm">
                            <button @click="editOpen=true; open=false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-800 text-gray-200 flex items-center gap-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit caption
                            </button>
                            <button @click="toggleArchive(); open=false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-800 text-gray-200 flex items-center gap-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/></svg>
                                <span x-text="isArchived ? 'Unarchive' : 'Archive'"></span>
                            </button>
                            <hr class="border-gray-800 my-1">
                            <button @click="confirmDelete=true; open=false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-800 text-red-400 flex items-center gap-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete post
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Edit caption modal --}}
        @if(auth()->id() === $post->user_id)
            <div x-show="editOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" x-cloak>
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 w-full max-w-md shadow-2xl" @click.away="editOpen=false">
                    <h3 class="text-sm font-bold text-white mb-3">Edit caption</h3>
                    <textarea x-model="editContent" rows="4" maxlength="1000"
                              class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-violet-500 resize-none"></textarea>
                    <p class="text-[10px] text-gray-600 mb-3 text-right" x-text="editContent.length + '/1000'"></p>
                    <div class="flex gap-2">
                        <button @click="editOpen=false" class="flex-1 py-2 rounded-xl text-sm bg-gray-800 hover:bg-gray-700 transition">Cancel</button>
                        <button @click="saveEdit()" class="flex-1 py-2 rounded-xl text-sm font-bold bg-violet-600 hover:bg-violet-700 transition">Save</button>
                    </div>
                </div>
            </div>

            {{-- Delete confirm modal --}}
            <div x-show="confirmDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" x-cloak>
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 w-full max-w-sm shadow-2xl" @click.away="confirmDelete=false">
                    <p class="text-sm font-bold text-white mb-1">Delete this post?</p>
                    <p class="text-xs text-gray-500 mb-4">This action cannot be undone.</p>
                    <div class="flex gap-2">
                        <button @click="confirmDelete=false" class="flex-1 py-2 rounded-xl text-sm bg-gray-800 hover:bg-gray-700 transition">Cancel</button>
                        <button @click="deletePost()" class="flex-1 py-2 rounded-xl text-sm font-bold bg-red-600 hover:bg-red-700 transition">Delete</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Archived notice --}}
        <div x-show="isArchived" class="mx-4 mb-3 bg-yellow-900/30 border border-yellow-700/40 rounded-xl px-3 py-2 text-xs text-yellow-400 flex items-center gap-2" x-cloak>
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12"/></svg>
            This post is archived — only you can see it.
        </div>

        {{-- Repost-of embed (quote repost origin) --}}
        @if($post->repostOf)
            <div class="mx-4 mb-3 bg-gray-900/60 border border-gray-700 rounded-xl p-3">
                <div class="flex items-center gap-2 mb-1.5">
                    <img src="{{ $post->repostOf->user->profile_picture_url }}" class="w-6 h-6 rounded-full object-cover" alt="">
                    <span class="text-xs font-semibold text-gray-300">{{ $post->repostOf->user->name }}</span>
                    <span class="text-xs text-gray-600">· {{ $post->repostOf->created_at->diffForHumans() }}</span>
                </div>
                @if($post->repostOf->content)
                    <p class="text-xs text-gray-400 leading-relaxed">{{ Str::limit($post->repostOf->content, 200) }}</p>
                @endif
                @if($post->repostOf->media_path)
                    <img src="{{ $post->repostOf->media_url }}" class="mt-2 w-full rounded-lg object-cover max-h-40" alt="">
                @endif
            </div>
        @endif

        {{-- Main content --}}
        @if($post->content)
            <div class="px-4 pb-3">
                <p class="text-base text-gray-100 leading-relaxed whitespace-pre-line" x-text="currentContent"></p>
            </div>
        @endif

        @if($post->media_path)
            <div class="border-t border-gray-800">
                <img src="{{ $post->media_url }}" class="w-full object-cover max-h-[600px]" alt="">
            </div>
        @endif

        {{-- Stats --}}
        <div class="flex items-center gap-5 px-4 py-3 border-t border-gray-800 text-xs text-gray-500">
            <span><strong class="text-gray-300" x-text="likesCount"></strong> likes</span>
            <span><strong class="text-gray-300" x-text="commentsCount"></strong> comments</span>
            <span><strong class="text-gray-300" x-text="repostsCount"></strong> reposts</span>
        </div>

        {{-- Actions --}}
        <div class="flex border-t border-gray-800">
            {{-- Like --}}
            <button @click="toggleLike()" class="flex-1 flex items-center justify-center gap-1.5 py-3 text-sm font-semibold transition"
                    :class="liked ? 'text-pink-500' : 'text-gray-500 hover:text-pink-400 hover:bg-gray-900/30'">
                <svg class="w-5 h-5" :fill="liked ? 'currentColor':'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span x-text="liked ? 'Liked' : 'Like'"></span>
            </button>

            {{-- Comment --}}
            <button onclick="document.getElementById('comment-input').focus()"
                    class="flex-1 flex items-center justify-center gap-1.5 py-3 text-sm font-semibold text-gray-500 hover:text-violet-400 hover:bg-gray-900/30 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Comment
            </button>

            {{-- Repost --}}
            <button @click="showRepostMenu=!showRepostMenu" class="flex-1 flex items-center justify-center gap-1.5 py-3 text-sm font-semibold transition relative"
                    :class="reposted ? 'text-green-400' : 'text-gray-500 hover:text-green-400 hover:bg-gray-900/30'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="repostsCount"></span>

                {{-- Repost dropdown --}}
                <div x-show="showRepostMenu" @click.away="showRepostMenu=false"
                     class="absolute bottom-12 left-1/2 -translate-x-1/2 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl z-20 py-1 w-44 text-left">
                    <button @click.stop="toggleRepost(); showRepostMenu=false"
                            class="w-full px-4 py-2.5 text-sm hover:bg-gray-800 text-gray-200 flex items-center gap-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-text="reposted ? 'Undo Repost' : 'Repost'"></span>
                    </button>
                    <button @click.stop="quoteOpen=true; showRepostMenu=false"
                            class="w-full px-4 py-2.5 text-sm hover:bg-gray-800 text-gray-200 flex items-center gap-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Quote Post
                    </button>
                </div>
            </button>

            {{-- Share --}}
            <button @click="copyLink()" class="flex-1 flex items-center justify-center gap-1.5 py-3 text-sm font-semibold text-gray-500 hover:text-cyan-400 hover:bg-gray-900/30 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                <span x-text="copied ? 'Copied!' : 'Share'"></span>
            </button>
        </div>

        {{-- Quote-repost modal --}}
        <div x-show="quoteOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" x-cloak>
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 w-full max-w-md shadow-2xl" @click.away="quoteOpen=false">
                <h3 class="text-sm font-bold text-white mb-3">Quote this post</h3>
                {{-- Original preview --}}
                <div class="bg-gray-950 border border-gray-800 rounded-xl p-3 mb-3">
                    <div class="flex items-center gap-2 mb-1.5">
                        <img src="{{ $post->user->profile_picture_url }}" class="w-5 h-5 rounded-full object-cover" alt="">
                        <span class="text-xs font-semibold text-gray-300">{{ $post->user->name }}</span>
                    </div>
                    @if($post->content)
                        <p class="text-xs text-gray-500 leading-relaxed">{{ Str::limit($post->content, 150) }}</p>
                    @endif
                    @if($post->media_path)
                        <img src="{{ $post->media_url }}" class="mt-1.5 w-full rounded-lg object-cover max-h-28" alt="">
                    @endif
                </div>
                <textarea x-model="quoteContent" placeholder="Add your comment…" rows="3" maxlength="1000"
                          class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-violet-500 resize-none mb-3"></textarea>
                <div class="flex gap-2">
                    <button @click="quoteOpen=false; quoteContent=''" class="flex-1 py-2 rounded-xl text-sm bg-gray-800 hover:bg-gray-700 transition">Cancel</button>
                    <button @click="submitQuote()" class="flex-1 py-2 rounded-xl text-sm font-bold bg-violet-600 hover:bg-violet-700 transition">Quote Post</button>
                </div>
            </div>
        </div>

        {{-- ── Comments ─────────────────────────────────────────────────── --}}
        <div class="border-t border-gray-800">
            {{-- Comment input --}}
            <div class="flex gap-3 px-4 py-4 border-b border-gray-800/50">
                <img src="{{ auth()->user()->profile_picture_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="Me">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 bg-gray-900 border border-gray-800 rounded-2xl px-4 py-2.5 focus-within:border-violet-500 transition">
                        <input id="comment-input" x-model="commentDraft" type="text"
                               placeholder="Write a comment…" maxlength="500"
                               class="flex-1 bg-transparent text-sm text-white placeholder-gray-600 border-0 focus:ring-0 focus:outline-none"
                               @keydown.enter.prevent="submitComment()">
                        <button @click="submitComment()" :disabled="!commentDraft.trim() || submitting"
                                class="text-violet-400 hover:text-violet-300 disabled:opacity-30 transition flex-shrink-0">
                            <svg class="w-5 h-5" :class="submitting ? 'animate-pulse':''" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </button>
                    </div>
                    <p x-show="commentError" x-text="commentError" class="text-xs text-red-400 mt-1.5" x-cloak></p>
                </div>
            </div>

            {{-- Comment list --}}
            <div class="divide-y divide-gray-800/40">
                {{-- Realtime new comments --}}
                <template x-for="c in newComments" :key="c.content + c.username">
                    <div class="flex gap-3 px-4 py-3.5 bg-violet-950/20">
                        <img :src="c.avatar" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-white" x-text="c.name"></span>
                                <span class="text-xs text-gray-500" x-text="'@' + c.username"></span>
                                <span class="text-[10px] text-gray-700 ml-auto">just now</span>
                            </div>
                            <p class="text-sm text-gray-200 leading-relaxed" x-text="c.content"></p>
                        </div>
                    </div>
                </template>

                {{-- Server-rendered comments --}}
                @forelse($post->comments as $comment)
                    <div class="flex gap-3 px-4 py-3.5 hover:bg-gray-900/20 transition">
                        <a href="{{ route('users.show', $comment->user->username) }}" class="flex-shrink-0">
                            <img src="{{ $comment->user->profile_picture_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                <a href="{{ route('users.show', $comment->user->username) }}" class="text-sm font-semibold text-white hover:underline">{{ $comment->user->name }}</a>
                                <span class="text-xs text-gray-500">@&nbsp;{{ $comment->user->username }}</span>
                                <span class="text-[10px] text-gray-700 ml-auto">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-200 leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10" id="no-comments-placeholder">
                        <p class="text-2xl mb-2">💬</p>
                        <p class="text-gray-500 text-sm">No comments yet — be the first!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Alpine logic --}}
        <script>
        function postDetail(postId, isLiked, likesCount, commentsCount, repostsCount, isReposted, isOwner) {
            return {
                liked: isLiked,
                likesCount,
                commentsCount,
                repostsCount,
                reposted: isReposted,
                isOwner,
                isArchived: {{ $post->is_archived ? 'true' : 'false' }},

                // Edit
                editOpen: false,
                editContent: `{{ addslashes($post->content ?? '') }}`,
                currentContent: `{{ addslashes($post->content ?? '') }}`,

                // Delete
                confirmDelete: false,

                // Comment
                commentDraft: '',
                submitting: false,
                commentError: null,
                newComments: [],

                // Repost
                showRepostMenu: false,
                quoteOpen: false,
                quoteContent: '',

                // Share
                copied: false,

                csrf() { return document.querySelector('meta[name="csrf-token"]').content; },

                async toggleLike() {
                    const res  = await fetch(`/posts/${postId}/like`, { method:'POST', headers:{ 'X-CSRF-TOKEN':this.csrf(), 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
                    const data = await res.json();
                    this.liked      = data.liked;
                    this.likesCount = data.likes_count;
                },

                async toggleRepost() {
                    const res  = await fetch(`/posts/${postId}/repost`, { method:'POST', headers:{ 'X-CSRF-TOKEN':this.csrf(), 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
                    const data = await res.json();
                    this.reposted      = data.reposted;
                    this.repostsCount  = data.reposts_count;
                },

                async submitQuote() {
                    if (!this.quoteContent.trim()) return;
                    const form = new FormData();
                    form.append('_token', this.csrf());
                    form.append('content', this.quoteContent);
                    const res  = await fetch(`/posts/${postId}/quote`, { method:'POST', body:form, headers:{ 'X-Requested-With':'XMLHttpRequest' } });
                    const data = await res.json();
                    if (data.success) {
                        this.quoteOpen   = false;
                        this.quoteContent= '';
                        this.repostsCount++;
                        window.location.href = data.post.show_url;
                    }
                },

                async submitComment() {
                    const content = this.commentDraft.trim();
                    if (!content) return;
                    this.submitting   = true;
                    this.commentError = null;
                    try {
                        const form = new FormData();
                        form.append('_token', this.csrf());
                        form.append('content', content);
                        const res  = await fetch(`/posts/${postId}/comment`, { method:'POST', body:form, headers:{ 'X-Requested-With':'XMLHttpRequest' } });
                        const data = await res.json();
                        if (!res.ok || !data.success) { this.commentError = data.error ?? 'Failed.'; return; }
                        document.getElementById('no-comments-placeholder')?.remove();
                        this.newComments.unshift({ name:data.comment.name, username:data.comment.username, avatar:data.comment.avatar, content:data.comment.content });
                        this.commentsCount = data.comments_count;
                        this.commentDraft  = '';
                    } catch { this.commentError = 'Network error.'; }
                    finally  { this.submitting = false; }
                },

                async saveEdit() {
                    if (!this.editContent.trim()) return;
                    const form = new FormData();
                    form.append('_token', this.csrf());
                    form.append('content', this.editContent);
                    const res  = await fetch(`/posts/${postId}`, { method:'POST', body:form, headers:{ 'X-Requested-With':'XMLHttpRequest', 'X-HTTP-Method-Override':'PATCH' } });
                    const data = await res.json();
                    if (data.success) { this.currentContent = data.content; this.editOpen = false; }
                },

                async toggleArchive() {
                    const res  = await fetch(`/posts/${postId}/archive`, { method:'POST', headers:{ 'X-CSRF-TOKEN':this.csrf(), 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
                    const data = await res.json();
                    if (data.success) this.isArchived = data.is_archived;
                },

                async deletePost() {
                    const res = await fetch(`/posts/${postId}`, { method:'POST', headers:{ 'X-CSRF-TOKEN':this.csrf(), 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-HTTP-Method-Override':'DELETE' } });
                    const data = await res.json();
                    if (data.success) window.location.href = '/dashboard';
                },

                copyLink() {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    });
                },
            };
        }
        </script>
    </div>

    {{-- Thread replies --}}
    @if($post->type === 'thread' && $post->replies->count())
        <div class="mb-5">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 px-1">Replies ({{ $post->replies->count() }})</h2>
            <div class="divide-y divide-gray-800/50 border border-gray-800 rounded-2xl overflow-hidden">
                @foreach($post->replies as $reply)
                    <a href="{{ route('posts.show', $reply->id) }}" class="flex gap-3 px-4 py-4 hover:bg-gray-900/30 transition block">
                        <img src="{{ $reply->user->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                <span class="text-sm font-semibold text-white">{{ $reply->user->name }}</span>
                                <span class="text-xs text-gray-500">@&nbsp;{{ $reply->user->username }}</span>
                                <span class="text-xs text-gray-600 ml-auto">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-200 leading-relaxed whitespace-pre-line">{{ $reply->content }}</p>
                            @if($reply->likes->count())
                                <div class="flex items-center gap-1 mt-2 text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    {{ $reply->likes->count() }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Reply box --}}
    @if($post->type === 'thread')
        <div class="bg-gray-900/40 border border-gray-800 rounded-2xl p-4">
            <h3 class="text-sm font-bold text-white mb-3">Reply to this thread</h3>
            <form action="{{ route('posts.reply', $post->id) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <img src="{{ auth()->user()->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="Me">
                    <div class="flex-1 min-w-0">
                        <textarea name="content" required placeholder="Post your reply…" rows="3" maxlength="1000"
                                  class="w-full bg-transparent border-0 focus:ring-0 text-white placeholder-gray-600 resize-none text-sm leading-relaxed no-scrollbar"></textarea>
                        <div class="flex justify-end mt-2 pt-2 border-t border-gray-800/60">
                            <button type="submit" class="px-5 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-full transition">Reply</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

</div>
</x-app-layout>
