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
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#4f46e5',
                    600: '#4338ca',
                    700: '#3730a3',
                    800: '#312e81',
                    900: '#1e1b4b',
                },
                // BankDash palette
                bank: {
                    blue: '#1814F3',      // Dark Blue (tombol utama, aktif menu)
                    navy: '#343C6A',      // Primary 2 (judul, teks sidebar)
                    bg: '#F5F7FA',        // Latar halaman
                    light: '#E6EFF5',     // Border/input lembut
                    gray: '#718EBF',      // Teks sekunder
                    red: '#FE5C73',       // Secondary (harga/aksi merah)
                    green: '#41D4A8',     // Aksen positif
                    yellow: '#FFBB38',    // Aksen kuning
                },
            },
        },
    },

    plugins: [forms],
};
