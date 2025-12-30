import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#0B5B7C',
                'primary-600': '#0A4E67',
                gold: '#F8B703',
                accent: '#F53003',
                muted: '#706F6C',
                background: '#FDFDFC',
                'background-dark': '#0A0A0A',
                zinzino: {
                    DEFAULT: '#0B5B7C',
                    dark: '#072f39',
                    gold: '#F8B703',
                    accent: '#F53003'
                }
            },
        },
    },

    plugins: [forms],
};
