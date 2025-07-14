import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {

    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
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
                primary700: '#1473E3',   
                primary100: '#EEF9FF',
                base400: '#888888',
                base300: '#B0B0B0',
                base200: '#D1D1D1',
                success500: '#26C176',   
                warning: '#facc15',   
                error500: '#EB484B',    
                shadow: '#334155',    
            },
            boxShadow: {
                soft: '0 2px 8px rgba(0, 0, 0, 0.1)',
                primary: '0 4px 10px rgba(14, 165, 233, 0.4)',
            }
        },
    },
    

    plugins: [forms],
    require: ['flowbite/plugin']
};
