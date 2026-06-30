<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vybe') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
            .glassmorphism {
                background: rgba(17, 24, 39, 0.7);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            /* Hide scrollbar for Chrome, Safari and Opera */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            /* Hide scrollbar for IE, Edge and Firefox */
            .no-scrollbar {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
        </style>
    </head>
    <body class="bg-gray-950 text-gray-100 antialiased min-h-screen">
        <div class="flex min-h-screen">
            <!-- Sidebar Navigation (Desktop) -->
            <aside class="hidden md:flex flex-col w-64 lg:w-72 fixed h-screen border-r border-gray-800 bg-gray-950 px-4 py-6 z-20">
                <!-- Logo -->
                <div class="px-4 mb-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="text-3xl font-extrabold tracking-wider bg-gradient-to-r from-white via-violet-300 to-white bg-clip-text text-transparent drop-shadow-lg">
                            VYBE
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white font-semibold' : 'text-gray-400 hover:bg-gray-900/50 hover:text-white' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Home</span>
                    </a>

                    <a href="{{ route('messages.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('messages.*') ? 'bg-gray-900 text-white font-semibold' : 'text-gray-400 hover:bg-gray-900/50 hover:text-white' }}">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            @php
                                $totalUnread = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                            @endphp
                            @if($totalUnread > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-pink-500 text-[10px] font-bold text-white">{{ $totalUnread }}</span>
                            @endif
                        </div>
                        <span>Messages</span>
                    </a>

                    <a href="{{ route('users.show', auth()->user()->username ?? 'unknown') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('users.show') && request()->route('user')->id === auth()->id() ? 'bg-gray-900 text-white font-semibold' : 'text-gray-400 hover:bg-gray-900/50 hover:text-white' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profile</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-gray-900 text-white font-semibold' : 'text-gray-400 hover:bg-gray-900/50 hover:text-white' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Settings</span>
                    </a>
                </nav>

                <!-- Profile summary / Logout -->
                <div class="mt-auto border-t border-gray-800 pt-4 flex items-center justify-between">
                    <a href="{{ route('users.show', auth()->user()->username ?? 'unknown') }}" class="flex items-center gap-3">
                        <img src="{{ auth()->user()->profile_picture_url }}" class="w-10 h-10 rounded-full border border-violet-500 object-cover" alt="Avatar">
                        <div class="truncate max-w-[120px]">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">@&nbsp;{{ auth()->user()->username ?? 'username' }}</p>
                        </div>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-400 p-2 rounded-lg hover:bg-gray-900/50 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Bottom Navigation (Mobile) -->
            <nav class="md:hidden fixed bottom-0 left-0 right-0 glassmorphism border-t border-gray-800 py-3 px-6 flex items-center justify-around z-30">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </a>
                <a href="{{ route('messages.index') }}" class="relative {{ request()->routeIs('messages.*') ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    @if($totalUnread > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-pink-500 text-[10px] font-bold text-white">{{ $totalUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('users.show', auth()->user()->username ?? 'unknown') }}" class="{{ request()->routeIs('users.show') && request()->route('user')->id === auth()->id() ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </nav>

            <!-- Main Content Area -->
            <div class="flex-1 md:ml-64 lg:ml-72 pb-20 md:pb-0">
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
