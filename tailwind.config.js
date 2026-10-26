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
                // Warm cream surfaces.
                cream: {
                    50: '#FEFCF7',
                    100: '#FBF6EC',
                    200: '#F5EAD3',
                    300: '#EBD9B4',
                },
                // Deep green: brand, headings, primary actions.
                primary: {
                    50: '#EFF7F1',
                    100: '#D8EBDD',
                    200: '#B3D6BD',
                    300: '#84BB95',
                    400: '#53986D',
                    500: '#357D52',
                    600: '#276541',
                    700: '#1F5035',
                    800: '#1A412C',
                    900: '#143323',
                    950: '#0B1F15',
                },
                // Warm orange: highlights, sale tags, secondary calls to action.
                accent: {
                    50: '#FFF5EB',
                    100: '#FFE6CC',
                    200: '#FFCB99',
                    300: '#FFAA61',
                    400: '#FF8A33',
                    500: '#F26E10',
                    600: '#D4540A',
                    700: '#A8400B',
                    800: '#873510',
                    900: '#6F2E10',
                },
                ink: {
                    DEFAULT: '#1F2A24',
                    muted: '#56665D',
                },
                // Decorative borders (cards, dividers) and accessible form-field borders (>= 3:1 on white).
                line: '#E8DEC8',
                stroke: '#8F8772',
            },
            fontFamily: {
                sans: ['"Be Vietnam Pro"', ...defaultTheme.fontFamily.sans],
                heading: ['Nunito', '"Be Vietnam Pro"', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                xl: '1rem',
                '2xl': '1.5rem',
                '3xl': '2rem',
            },
            boxShadow: {
                soft: '0 2px 14px -2px rgb(31 80 53 / 0.12)',
                lift: '0 14px 34px -10px rgb(31 80 53 / 0.28)',
            },
        },
    },

    plugins: [forms],
};
