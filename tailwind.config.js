import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontWeight: {
                regular: '400',
                medium: '500',
                semibold: '600',
                bold: '700',
            },
            borderRadius: {
                sm: '6px',
                DEFAULT: '8px',
                lg: '16px',
                xl: '24px',
                '2xl': '32px',
                full: '9999px',
            },
            colors: {
                text: '#374651',
                primary: {
                    100: '#EEF9FF',
                    700: '#1473E3',
                },
                base: {
                    200: '#D1D1D1',
                    300: '#B0B0B0',
                    400: '#888888',
                },
                success: {
                    500: '#26C176',
                },
                warning: '#facc15',
                error: {
                    500: '#EB484B',
                },
                shadow: '#334155',
            },
            boxShadow: {
                soft: '0 2px 8px rgba(0, 0, 0, 0.1)',
                primary: '0 4px 10px rgba(14, 165, 233, 0.4)',
            }
        },
    },

    plugins: [
        forms,
        require('flowbite/plugin'),
        require('flowbite-typography'),
    ],
};
