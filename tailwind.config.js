import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [
                    'Plus Jakarta Sans',
                    'Inter',
                    'Manrope',
                    ...defaultTheme.fontFamily.sans,
                ],
                display: ['Plus Jakarta Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                surface: {
                    50:  '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b',
                    950: '#09090b',
                },
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'mesh-1':
                    'radial-gradient(at 0% 0%, rgba(99,102,241,0.25) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(168,85,247,0.22) 0px, transparent 50%), radial-gradient(at 50% 100%, rgba(34,211,238,0.22) 0px, transparent 50%)',
                'mesh-2':
                    'radial-gradient(at 20% 20%, rgba(236,72,153,0.20) 0px, transparent 50%), radial-gradient(at 80% 0%, rgba(99,102,241,0.25) 0px, transparent 50%), radial-gradient(at 60% 100%, rgba(34,211,238,0.20) 0px, transparent 50%)',
            },
            boxShadow: {
                glow: '0 0 0 1px rgba(99,102,241,0.18), 0 8px 30px -8px rgba(99,102,241,0.35)',
                'glow-lg': '0 10px 40px -10px rgba(99,102,241,0.55)',
                card: '0 1px 2px rgba(15,23,42,0.04), 0 4px 16px -4px rgba(15,23,42,0.06)',
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.125rem',
                '3xl': '1.5rem',
            },
            keyframes: {
                'fade-in': {
                    '0%': { opacity: 0, transform: 'translateY(4px)' },
                    '100%': { opacity: 1, transform: 'translateY(0)' },
                },
                'scale-in': {
                    '0%': { opacity: 0, transform: 'scale(0.97)' },
                    '100%': { opacity: 1, transform: 'scale(1)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                float: {
                    '0%,100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-6px)' },
                },
            },
            animation: {
                'fade-in': 'fade-in 0.25s ease-out both',
                'scale-in': 'scale-in 0.2s ease-out both',
                shimmer: 'shimmer 1.6s linear infinite',
                float: 'float 5s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
