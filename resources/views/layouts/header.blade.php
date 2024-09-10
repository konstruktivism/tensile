<nav class="navbar navbar-expand-md navbar-light dark:bg-dark bg-white shadow-sm p-6">
    <div class="flex justify-between">
        <div class="flex items-center gap-3">
            <a class="font-bold tracking-tight text-2xl lowercase" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

            /

            <a href="{{ route('projects') }}" class="dark:text-gray-50 hover:underline">Projecten</a>

            /

            <a href="{{ route('projects') }}" class="dark:text-gray-50 hover:underline">V2</a>
        </div>

        @guest
            <a href="/login" class="underline">Login</a>
        @endguest
        @auth
            <div class="flex gap-1.5 items-center">
                Hi Sander

                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        @endauth
    </div>
</nav>
