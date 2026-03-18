import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/js/**/*.{js,jsx}',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                site: {
                    bg: '#141414',
                    card: '#191919',
                    'card-alt': '#171717',
                    border: '#1e1e1e',
                    'border-light': '#252525',
                    text: '#ece8e0',
                    'text-muted': '#d0ccc4',
                    'text-faint': '#585858',
                    'text-ghost': '#404040',
                    amber: '#c4924a',
                    'amber-dark': '#9a6e34',
                },
            },
            fontFamily: {
                serif: ['Georgia', 'serif'],
                sans: ['system-ui', 'sans-serif'],
            },
            boxShadow: {
                hairline: '0 0 0 1px rgba(30, 30, 30, 0.9)',
            },
            letterSpacing: {
                editorial: '0.2em',
            },
        },
    },
    plugins: [typography],
};
