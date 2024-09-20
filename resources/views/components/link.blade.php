<a href="{{ $url }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-md text-center px-4 py-2 border border-transparent rounded-full font-bold hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
