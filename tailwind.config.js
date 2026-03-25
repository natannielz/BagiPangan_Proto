import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    400: '#4ade80',
                    600: '#16a34a',
                    800: '#166534',
                },
                sage: {
                    50: '#f1f8f1',
                    100: '#d4edda',
                    200: '#a8d5b0',
                    400: '#6aaf78',
                    600: '#3d8b50',
                    800: '#1a4a2a',
                },
                'warm-white': '#fafaf5',
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
        },
    },

    plugins: [forms],
};
