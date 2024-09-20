import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'media', // or 'media'

    theme: {
        extend: {
            fontFamily: {
                sans: ['Albert Sans', ...defaultTheme.fontFamily.sans],
                mono: ['Space Mono', ...defaultTheme.fontFamily.mono],
            },
            backgroundColor: {
                dark: '#1b1b1b', // or any color you prefer
            },
            colors: {
                dark: '#1b1b1b', // or any color you prefer
                primary: '#1b1b1b', // example primary color
                secondary: '#a5a5a5', // example secondary color
                tertiary: '#38b2ac', // example tertiary color
                quaternary: '#e3342f', // example quaternary color
            },
            dropShadow: {
                px: '0px 1px 0px rgba(0, 0, 0, 0.10)',
            },
            animation: {
                fadeIn: 'fadeIn 0.3s ease-in-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: 0 },
                    '100%': { opacity: 1 },
                },
            },
        },
    },

    plugins: [forms],
};
