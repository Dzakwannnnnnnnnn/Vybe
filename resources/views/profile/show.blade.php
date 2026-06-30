<x-app-layout>
    <div class="max-w-7xl mx-auto flex" x-data="{ profileTab: 'feed' }">
        <main class="flex-1 max-w-2xl border-r border-gray-800 min-h-screen pb-24">
            <header class="flex items-center gap-5 px-4 py-3 sticky top-0 bg-gray-950/90 backdrop-blur z-20 border-b border-gray-800/50">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                   class="text-gray-400 hover:text-white transition"
                   aria-label="Back">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-white leading-tight truncate">{{ $user->name }}</h1>
                    <p class="text-xs text-gray-500">{{ $posts->count() }} {{ Str::plural('post', $posts->count()) }}</p>
                </div>
            </header>

            <section>
                <div class="h-44 bg-gradient-to-r from-violet-900 via-fuchsia-900 to-pink-900"></div>

                <div class="px-4">
                    <div class="flex justify-between">
                        <div class="-mt-14 rounded-full p-[3px] bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 shadow-xl overflow-hidden"
                             style="width: 7rem; height: 7rem;">
                            <img src="{{ $user->profile_picture_url }}"
                                 class="w-full h-full rounded-full object-cover border-4 border-gray-950"
                                 alt="{{ $user->name }}">
                        </div>

                        <div class="pt-4">
                            @if(auth()->id() === $user->id)
                                <a href="{{ route('profile.edit') }}"
                                   class="inline-flex items-center border border-gray-700 hover:bg-gray-900 text-white text-xs font-bold py-2 px-5 rounded-full transition">
                                    Edit Profile
                                </a>
                            @else
                                <form action="{{ route('follow.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-white hover:bg-gray-100 text-black text-xs font-bold py-2 px-5 rounded-full transition">
                                        {{ auth()->user()->isFollowing($user) ? 'Unfollow' : 'Follow' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div>
                            <h2 class="text-xl font-black text-white tracking-tight">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-500">@&nbsp;{{ $user->username }}</p>
                        </div>

                        <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-line"><x-linked-bio :text="$user->bio" /></p>

                        <div class="flex items-center gap-5 text-sm">
                            <span class="text-gray-500"><strong class="text-white">{{ $user->following_count }}</strong> Following</span>
                            <span class="text-gray-500"><strong class="text-white">{{ $user->followers_count }}</strong> Followers</span>
                        </div>
                    </div>
                </div>
            </section>

            <nav class="flex border-b border-gray-800 sticky top-[53px] bg-gray-950/90 backdrop-blur z-10 mt-6">
                <button type="button"
                        class="flex-1 py-3.5 text-xs font-bold border-b-2 transition"
                        :class="profileTab === 'feed' ? 'border-violet-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300'"
                        @click="profileTab = 'feed'">
                    Feed
                </button>
                <button type="button"
                        class="flex-1 py-3.5 text-xs font-bold border-b-2 transition"
                        :class="profileTab === 'thread' ? 'border-violet-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300'"
                        @click="profileTab = 'thread'">
                    Threads
                </button>
            </nav>

            <section x-show="profileTab === 'feed'" class="divide-y divide-gray-800/50">
                @forelse($posts->where('type', 'feed') as $post)
                    <article class="py-4 px-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <img src="{{ $user->profile_picture_url }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="{{ $user->name }}">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white leading-none truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">@&nbsp;{{ $user->username }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-600 flex-shrink-0 ml-3">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        @if($post->media_path)
                            <div class="rounded-xl overflow-hidden border border-gray-800 mb-3">
                                <img src="{{ $post->media_url }}" class="w-full object-cover max-h-[500px]" alt="Post image">
                            </div>
                        @endif

                        @if($post->content)
                            <p class="text-sm text-gray-200 mb-3 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                        @endif

                        <div class="flex items-center gap-5 text-xs text-gray-500">
                            <span>{{ $post->likes_count }} {{ Str::plural('like', $post->likes_count) }}</span>
                            <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
                            <span>{{ $post->reposts_count }} {{ Str::plural('repost', $post->reposts_count) }}</span>
                        </div>
                    </article>
                @empty
                    <div class="px-4 py-14 text-center">
                        <p class="text-sm font-semibold text-white">No feed posts yet</p>
                        <p class="text-sm text-gray-500 mt-1">When {{ $user->name }} shares photos, they will appear here.</p>
                    </div>
                @endforelse
            </section>

            <section x-show="profileTab === 'thread'" class="divide-y divide-gray-800/50" x-cloak>
                @forelse($posts->where('type', 'thread') as $post)
                    <article class="px-4 py-4 hover:bg-gray-900/30 transition cursor-pointer"
                             onclick="window.location='{{ route('posts.show', $post->id) }}'">
                        <div class="flex gap-3">
                            <img src="{{ $user->profile_picture_url }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0" alt="{{ $user->name }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="text-sm font-semibold text-white truncate">{{ $user->name }}</span>
                                        <span class="text-xs text-gray-500 flex-shrink-0">@&nbsp;{{ $user->username }}</span>
                                    </div>
                                    <span class="text-xs text-gray-600 flex-shrink-0">{{ $post->created_at->diffForHumans() }}</span>
                                </div>

                                @if($post->content)
                                    <p class="text-sm text-gray-200 mt-1.5 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                                @endif

                                <div class="flex items-center gap-5 text-xs text-gray-500 mt-3">
                                    <span>{{ $post->likes_count }} {{ Str::plural('like', $post->likes_count) }}</span>
                                    <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
                                    <span>{{ $post->reposts_count }} {{ Str::plural('repost', $post->reposts_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="px-4 py-14 text-center">
                        <p class="text-sm font-semibold text-white">No threads yet</p>
                        <p class="text-sm text-gray-500 mt-1">When {{ $user->name }} posts a thread, it will appear here.</p>
                    </div>
                @endforelse
            </section>
        </main>
    </div>
</x-app-layout>
