@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-neutral-600 dark:neutral-gray-700 dark:bg-neutral-900 dark:text-gray-300 focus:border-yellow-400 dark:focus:border-yellow-600 focus:ring-yellow-400 dark:focus:ring-yellow-600 rounded-md shadow-sm']) !!}>
