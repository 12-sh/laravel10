<x-guest-layout>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    {{ $social->name }}の情報を連携しますか？
  </div>

  <form method="POST" action="{{ route('socialite.register', ['social' => $social->driver]) }}">
      @csrf
      <input type="hidden" name="social_user_id" value="{{ $socialiteUser->id }}">
      <!-- Name -->
      <div>
          <x-input-label for="name" :value="__('Name')" />
          {{ optional($user)->name ?? '新規' }} <
          <x-text-input 
            id="name"
            class="block mt-1 w-full"
            type="text"
            name="socialite-name" 
            :value="old('name', {{ $socialiteUser->name }})"
            required
            autofocus
            autocomplete="name"
            readonly
          />
          <x-input-error :messages="$errors->get('name')" class="mt-2" />
      </div>

      <!-- Email Address -->
      <div class="mt-4">
          <x-input-label for="email" :value="__('Email')" />
          {{ optional($user)->email ?? '新規' }} <
          <x-text-input
            id="email"
            class="block mt-1 w-full"
            type="email"
            name="email"
            :value="old('email')"
            required
            autocomplete="username"
            readonly
          />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="flex items-center justify-end mt-4">
          <a
            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            href="{{ route('login') }}"
          >
              {{ __('Cancel') }}
          </a>

          <x-primary-button class="ms-4">
              {{ __('Register') }}
          </x-primary-button>
      </div>
  </form>
</x-guest-layout>
