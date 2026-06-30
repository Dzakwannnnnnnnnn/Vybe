<x-app-layout>
    <div class="max-w-7xl mx-auto min-h-screen flex">
        <aside class="w-full md:w-80 lg:w-96 border-r border-gray-800 min-h-screen {{ $activeUser ? 'hidden md:block' : 'block' }}">
            <div class="px-4 py-4 sticky top-0 bg-gray-950/90 backdrop-blur z-10 border-b border-gray-800">
                <h1 class="text-xl font-black text-white">Messages</h1>
                <p class="text-xs text-gray-500 mt-1">Private conversations</p>
            </div>

            <div class="divide-y divide-gray-800/60">
                @forelse($chats as $chat)
                    <a href="{{ route('messages.show', $chat->id) }}"
                       class="flex items-center gap-3 px-4 py-4 transition {{ $activeUser?->id === $chat->id ? 'bg-gray-900/80' : 'hover:bg-gray-900/40' }}">
                        <img src="{{ $chat->profile_picture_url }}"
                             class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                             alt="{{ $chat->name }}">

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-bold text-white truncate">{{ $chat->name }}</p>
                                @if($chat->last_message)
                                    <span class="text-[11px] text-gray-600 flex-shrink-0">{{ $chat->last_message->created_at->diffForHumans(null, true) }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3 mt-1">
                                <p class="text-xs truncate {{ $chat->unread_count ? 'text-gray-200 font-semibold' : 'text-gray-500' }}">
                                    @if($chat->last_message?->sender_id === auth()->id())
                                        You:
                                    @endif
                                    {{ $chat->last_message?->content ?? 'No messages yet' }}
                                </p>

                                @if($chat->unread_count)
                                    <span class="min-w-5 h-5 px-1.5 rounded-full bg-pink-500 text-white text-[11px] font-bold flex items-center justify-center">
                                        {{ $chat->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-16 text-center">
                        <div class="w-14 h-14 rounded-full bg-gray-900 border border-gray-800 mx-auto flex items-center justify-center text-gray-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-white mt-4">No conversations yet</p>
                        <p class="text-sm text-gray-500 mt-1">Messages will appear here after you start chatting.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <main class="flex-1 min-h-screen {{ $activeUser ? 'flex' : 'hidden md:flex' }} flex-col">
            @if($activeUser)
                <header class="flex items-center gap-3 px-4 py-3 sticky top-0 bg-gray-950/90 backdrop-blur z-10 border-b border-gray-800">
                    <a href="{{ route('messages.index') }}" class="md:hidden text-gray-400 hover:text-white transition" aria-label="Back to conversations">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                        </svg>
                    </a>

                    <a href="{{ $activeUser->username ? route('users.show', $activeUser->username) : '#' }}" class="flex items-center gap-3 min-w-0">
                        <img src="{{ $activeUser->profile_picture_url }}"
                             class="w-10 h-10 rounded-full object-cover"
                             alt="{{ $activeUser->name }}">
                        <div class="min-w-0">
                            <h2 class="text-sm font-bold text-white truncate">{{ $activeUser->name }}</h2>
                            <p class="text-xs text-gray-500 truncate">@&nbsp;{{ $activeUser->username ?? 'user' }}</p>
                        </div>
                    </a>
                </header>

                <section class="flex-1 px-4 py-5 space-y-3 overflow-y-auto">
                    @forelse($messages as $message)
                        @php $isMine = $message->sender_id === auth()->id(); @endphp

                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[78%] sm:max-w-[68%]">
                                <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed whitespace-pre-line break-words {{ $isMine ? 'bg-violet-600 text-white rounded-br-md' : 'bg-gray-900 text-gray-100 border border-gray-800 rounded-bl-md' }}">{{ $message->content }}</div>
                                <p class="text-[11px] text-gray-600 mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                                    {{ $message->created_at->format('M j, g:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center text-center py-20">
                            <div>
                                <p class="text-sm font-semibold text-white">Start the conversation</p>
                                <p class="text-sm text-gray-500 mt-1">Send a message to {{ $activeUser->name }}.</p>
                            </div>
                        </div>
                    @endforelse
                </section>

                <footer class="sticky bottom-0 bg-gray-950/95 backdrop-blur border-t border-gray-800 p-4">
                    <form action="{{ route('messages.store', $activeUser->id) }}" method="POST" class="flex items-end gap-3">
                        @csrf
                        <textarea name="content"
                                  rows="1"
                                  maxlength="2000"
                                  required
                                  placeholder="Message {{ $activeUser->name }}"
                                  class="flex-1 max-h-36 resize-none rounded-2xl border border-gray-800 bg-gray-900 px-4 py-3 text-sm text-white placeholder-gray-600 focus:border-violet-500 focus:ring-violet-500"></textarea>
                        <button type="submit"
                                class="h-11 w-11 rounded-full bg-violet-600 hover:bg-violet-700 text-white flex items-center justify-center transition flex-shrink-0"
                                aria-label="Send message">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 14-7-7 14-2-5-5-2z"/>
                            </svg>
                        </button>
                    </form>

                    @error('content')
                        <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                    @enderror
                </footer>
            @else
                <div class="flex-1 hidden md:flex items-center justify-center text-center px-6">
                    <div>
                        <div class="w-16 h-16 rounded-full bg-gray-900 border border-gray-800 mx-auto flex items-center justify-center text-gray-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <p class="text-base font-bold text-white mt-4">Select a conversation</p>
                        <p class="text-sm text-gray-500 mt-1">Choose a chat from the inbox to read and reply.</p>
                    </div>
                </div>
            @endif
        </main>
    </div>
</x-app-layout>
