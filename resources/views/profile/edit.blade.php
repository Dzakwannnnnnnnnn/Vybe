<x-app-layout>
    <div class="max-w-7xl mx-auto flex">
        <main class="flex-1 max-w-4xl min-h-screen pb-24">
            <header class="px-4 sm:px-6 py-5 border-b border-gray-800">
                <p class="text-xs font-semibold text-violet-400 uppercase tracking-wide">Account</p>
                <h1 class="text-2xl font-black text-white mt-1">Profile Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your public Vybe profile and account security.</p>
            </header>

            <section class="px-4 sm:px-6 py-6 border-b border-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="rounded-full p-[3px] bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 shadow-xl flex-shrink-0 overflow-hidden"
                         style="width: 7rem; height: 7rem;">
                        <img src="{{ $user->profile_picture_url }}"
                             class="w-full h-full rounded-full object-cover border-4 border-gray-950"
                             alt="{{ $user->name }}">
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-xl font-black text-white truncate">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500">@&nbsp;{{ $user->username }}</p>
                        <p class="text-sm text-gray-300 mt-3 max-w-2xl whitespace-pre-line"><x-linked-bio :text="$user->bio" /></p>

                        <div class="flex flex-wrap items-center gap-4 mt-4 text-sm">
                            <span class="text-gray-500"><strong class="text-white">{{ $user->posts_count }}</strong> Posts</span>
                            <span class="text-gray-500"><strong class="text-white">{{ $user->followers_count }}</strong> Followers</span>
                            <span class="text-gray-500"><strong class="text-white">{{ $user->following_count }}</strong> Following</span>
                        </div>
                    </div>

                    <a href="{{ route('users.show', $user->username) }}"
                       class="inline-flex items-center justify-center rounded-full border border-gray-700 px-5 py-2 text-xs font-bold text-white hover:bg-gray-900 transition">
                        View Profile
                    </a>
                </div>
            </section>

            <div class="px-4 sm:px-6 py-6 space-y-6">
                <section class="border border-gray-800 bg-gray-900/30 rounded-lg p-4 sm:p-6">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </section>

                <section class="border border-gray-800 bg-gray-900/30 rounded-lg p-4 sm:p-6">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </section>

                <section class="border border-red-900/60 bg-red-950/20 rounded-lg p-4 sm:p-6">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
