<footer class="mb-3 text-center text-lg-start rounded-full">
    <div class="w-full h-8 shadow-inner dark:shadow-neutral-800 relative">
        <div
            class="absolute inset-y-0 left-0 bg-gradient-to-r from-white dark:from-dark to-transparent w-1/4 h-full z-10">
        </div>

        <div
            class="absolute inset-y-0 right-0 bg-gradient-to-l from-white dark:from-dark to-transparent w-1/4 h-full z-10">
        </div>
    </div>

    <div class="flex flex-col items-center gap-6 md:gap-0 md:flex-row justify-between px-6 pb-3 text-neutral-500">
        {{ config('app.slogan') }}

        <div class="flex items-center gap-2 text-md text-dark dark:text-neutral-500">
            <a class="text-black font-bold rounded-br-lg px-2 bg-yellow-400 lowercase" href="{{ url('/') }}">{{
                config('app.name') }}</a>

            <a href="https://konstruktiv.nl" target="_blank" class="flex gap-2">
                built by

                <div class="fill-current flex items-center gap-2">
                    <svg width="13" height="13" viewBox="0 0 1000 1000" fill="none" class="fill-current"
                        xmlns="http://www.w3.org/2000/svg">
                        <g id="">
                            <g id="Vector">
                                <path d="M0 1000H500V0H0V1000Z" fill="currentColor"></path>
                                <path d="M500 751L1000 1000L1000 0L500 751Z" fill="currentColor">
                                    <animate attributeName="d"
                                        values="M500 751L1000 1000L1000 0L500 751Z;M500 250L1000 1000L1000 0L500 250Z;M500 751L1000 1000L1000 0L500 751Z;M500 250L1000 1000L1000 0L500 250Z;M500 751L1000 1000L1000 0L500 751Z;M500 250L1000 1000L1000 0L500 250Z;M500 751L1000 1000L1000 0L500 751Z;M500 250L1000 1000L1000 0L500 250Z;M500 751L1000 1000L1000 0L500 751Z"
                                        begin="0s" dur="30s" repeatCount="indefinite" fill="freeze" calcMode="linear"
                                        keyTimes="0;0.125;0.25;0.375;0.5;0.625;0.75;0.875;1">
                                    </animate>
                                </path>
                            </g>
                        </g>
                    </svg>

                    Konstruktiv
                </div>
            </a>
        </div>
    </div>
</footer>