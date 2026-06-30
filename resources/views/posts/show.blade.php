<x-app-layout>
<div class="max-w-7xl mx-auto flex" x-data="vybeProfile()">

    {{-- ════════════════════════════ MAIN PROFILE CONTENT ════════════════════════════ --}}
    <div class="flex-1 max-w-2xl border-r border-gray-800 min-h-screen pb-24">
        
        <div class="flex items-center gap-6 px-4 py-3 sticky top-0 bg-gray-950/90 backdrop-blur z-20 border-b border-gray-800/50">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-base font-bold text-white leading-tight">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500">{{ $posts->count() }} Posts</p>
            </div>
        </div>

        <div class="relative">
            <div class="h-44 bg-gradient-to-r from-violet-900 to-pink-900 w-full"></div>
            <div class="absolute -bottom-14 left-4">
                <div class="w-28 h-28 rounded-full p-[3px] bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 shadow-xl">
                    <img src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" class="w-full h-full rounded-full object-cover border-4 border-gray-950" alt="">
                </div>
            </div>
        </div>

        <div class="flex justify-end px-4 pt-4 h-14">
            @if(auth()->id() === $user->id)
                <button @click="editProfileOpen = true" class="border border-gray-700 hover:bg-gray-900 text-white text-xs font-bold py-2 px-5 rounded-full transition h-fit">Edit Profile</button>
            @else
                <form action="{{ route('follow.toggle', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white text-black text-xs font-bold py-2 px-5 rounded-full transition">
                        {{ auth()->user()->isFollowing($user) ? 'Unfollow' : 'Follow' }}
                    </button>
                </form>
            @endif
        </div>

        <div class="px-4 mt-3 space-y-3">
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500">@&nbsp;{{ $user->username }}</p>
            </div>
            <p class="text-sm text-gray-300 leading-relaxed">{{ $user->bio ?? 'No bio yet.' }}</p>
        </div>

        {{-- Tabs Menu --}}
        <div class="flex border-b border-gray-800 sticky top-[53px] bg-gray-950/90 backdrop-blur z-10 mt-6">
            <button class="flex-1 py-3.5 text-xs font-bold border-b-2 transition" :class="profileTab==='feed' ? 'border-violet-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300'" @click="profileTab='feed'">Feed Posts</button>
            <button class="flex-1 py-3.5 text-xs font-bold border-b-2 transition" :class="profileTab==='thread' ? 'border-violet-500 text-white' : 'border-transparent text-gray-500 hover:text-gray-300'" @click="profileTab='thread'">Threads</button>
        </div>

        {{-- Konten Berdasarkan Tab --}}
        <div x-show="profileTab==='feed'" class="divide-y divide-gray-800/50">
            @each('partials.post', $posts->where('type', 'feed'), 'post', 'partials.empty-feed')
        </div>

        <div x-show="profileTab==='thread'" class="divide-y divide-gray-800/50" x-cloak>
            @each('partials.post', $posts->where('type', 'thread'), 'post', 'partials.empty-thread')
        </div>
    </div>
</div>

<script>
function vybeProfile() { 
    return { 
        profileTab: 'feed', 
        editProfileOpen: false 
    } 
}
</script>
</x-app-layout>