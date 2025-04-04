<nav class="navbar navbar-expand-md navbar-light dark:bg-dark bg-white drop-shadow dark:drop-shadow-lg dark:shadow-neutral-950 p-6">
    <div class="flex justify-between">
        <div class="flex items-center gap-3">
            @auth()
                <a class="font-bold tracking-tight text-2xl lowercase" href="{{  route('projects') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                /

                <a href="{{ route('projects') }}" class="dark:text-gray-50 hover:underline">Projects</a>

                @if(request()->routeIs('project') || request()->routeIs('project.viewWeek'))
                    /

                    <a href="{{ route('project', ['project' => $project->id]) }}" class="dark:text-gray-50 hover:underline flex items-center gap-3">
                        <div class="text-xs px-3 py-1 uppercase rounded font-bold no-underline hover:no-underline {{ $project->organisation->name == 'Konstruktiv' ? 'bg-yellow-400 text-black' : 'bg-blue-600 text-white' }}">
                            {{ $project->organisation->name }}
                        </div>

                        {{ $project->name }}
                    </a>
                @endif
            @endauth

            @guest
                <a class="font-bold tracking-tight text-2xl lowercase" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            @endguest
        </div>

        @guest
            <x-link url="{{ route('login') }}" class="bg-white text-neutral-600 font-normal border border-neutral-200 hover:bg-neutral-100 active:bg-neutral-200 ring-0 focus:bg-transparent">Login</x-link>
        @endguest
        @auth
            <div class="relative flex items-center§" x-data="{ open: false }">
                <button @click="open = !open" class="flex gap-1.5 items-center focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    Hi {{ Auth::user()->name }}

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition-opacity ease-out duration-200" x-transition:leave="transition-opacity ease-in duration-150" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg p-1 bg-white dark:bg-neutral-800 ring-1 ring-black ring-opacity-5" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" id="user-menu">
                    <form method="POST" action="{{ route('logout') }}" class="flex">
                        @csrf
                        <button type="submit" class="rounded flex w-full px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-neutral-700" role="menuitem">Logout</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
