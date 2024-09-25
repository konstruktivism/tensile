<x-app-layout>
    <div class="w-full bg-white dark:bg-dark h-full flex grow flex-col justify-stretch md:flex-row p-3 md:p-12">
        <div class="bg-white dark:bg-dark lg:w-1/2 p-6 lg:p-12 flex flex-col justify-center gap-3 md:gap-12">
            <!-- Session Status -->
            <x-auth-session-status  :status="session('status')" />

            <h1 class="text-6xl font-bold mb-4 tracking-tight">Login</h1>

            <form method="POST" action="{{ route('login.magic.send') }}" class="flex flex-col gap-3">
                @csrf
                <x-text-input id="email" type="email" name="email" placeholder="{{ __('Email') }}" required autofocus autocomplete="username" />

                <x-button type="submit" class="bg-primary">{{ __('Login via e-mail') }}</x-button>
            </form>

            <hr />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="hidden" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="{{ __('Email') }}" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="hidden" />

                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password"
                                  name="password"
                                  placeholder="{{ __('Password') }}"
                                  required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary shadow-sm focus:ring-primary dark:focus:ring-primary dark:focus:ring-offset-gray-800" name="remember" checked>
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-button class="ms-3 bg-primary">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </div>
        <div class="hidden md:flex flex-col justify-center w-1/2">
            <picture class="rounded-xl h-full overflow-hidden bg-gradient-to-br from-neutral-800 to-primary">
                <source srcset="{{ asset('/img/bg.jpg') }}" type="image/jpg">

                <img src="{{ asset('/img/bg.jpg') }}" alt="" class="object-cover w-full h-full" />
            </picture>
        </div>
    </div>
</x-app-layout>
