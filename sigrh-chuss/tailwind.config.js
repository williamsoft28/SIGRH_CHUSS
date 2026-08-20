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
            colors: {
                'chuss-green': '#0F3D2E',
                'chuss-green-light': '#1a5c46',
                'chuss-amber': '#EF9F27',
                'chuss-cream': '#FAF7F0',
                'chuss-dark': '#111827',
                'chuss-gray': '#6B7280',
            },
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'glass': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                'float': '0 20px 40px -15px rgba(0,0,0,0.05)',
            }
        },
    },

    plugins: [forms],
};
