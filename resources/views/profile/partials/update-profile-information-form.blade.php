<section>
    <header>
        <h2 class="text-lg font-bold text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ __("Update your public profile, avatar, and account email.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center gap-4">
            <div class="rounded-full p-[2px] bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 flex-shrink-0 overflow-hidden"
                 style="width: 5rem; height: 5rem;">
                <img src="{{ $user->profile_picture_url }}" class="w-full h-full rounded-full object-cover border-2 border-gray-950" alt="{{ $user->name }}">
            </div>

            <div class="flex-1">
                <x-input-label for="profile_picture" :value="__('Profile Photo')" />
                <input id="profile_picture"
                       name="profile_picture"
                       type="file"
                       accept="image/*"
                       class="mt-2 block w-full text-sm text-gray-400 file:mr-4 file:rounded-full file:border-0 file:bg-violet-600 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-violet-700" />
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <div class="mt-1 flex rounded-md shadow-sm">
                <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-700 bg-gray-900 px-3 text-sm text-gray-500">@</span>
                <x-text-input id="username" name="username" type="text" class="block w-full rounded-l-none" :value="old('username', $user->username)" required autocomplete="username" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio"
                      name="bio"
                      rows="4"
                      maxlength="500"
                      class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500"
                      placeholder="Tell people about your Vybe.">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-violet-300"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
