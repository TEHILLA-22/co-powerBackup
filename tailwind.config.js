// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                copower: {
                    dark: '#0F3D5E',      // Brand dark navy/teal
                    banner: '#00A3E0',    // Bright sky blue
                    gray: '#F8F9FA',
                }
            },
            fontFamily: {
                sans: ['Figtree', 'sans-serif'],
            }
        },
    },
    plugins: [],
}