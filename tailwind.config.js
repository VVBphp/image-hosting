const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        darkSelector: '.dark',
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    variants: {
        backgroundColor: ['dark', 'dark-hover', 'dark-group-hover', 'dark-even', 'dark-odd', 'hover', 'responsive'],
        borderColor: ['dark', 'dark-focus', 'dark-focus-within', 'hover', 'responsive'],
        textColor: ['dark', 'dark-hover', 'dark-active', 'hover', 'responsive'],
        boxShadow: ['dark','responsive', 'group-hover', 'focus-within', 'hover', 'focus'],
        extend: {
            cursor: ['disabled','hover']
        }
    },

    plugins: [require('@tailwindcss/forms')],
};
