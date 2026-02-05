<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PINDANG OI') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Theme Store untuk Guest Layout -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('theme', {
                    init() {
                        const savedTheme = localStorage.getItem('theme');
                        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                        this.theme = savedTheme || systemTheme;
                        this.updateTheme();
                    },
                    theme: 'light',
                    toggle() {
                        this.theme = this.theme === 'light' ? 'dark' : 'light';
                        localStorage.setItem('theme', this.theme);
                        this.updateTheme();
                    },
                    updateTheme() {
                        const html = document.documentElement;
                        const body = document.body;
                        if (this.theme === 'dark') {
                            html.classList.add('dark');
                            body.classList.add('dark', 'bg-gray-900');
                        } else {
                            html.classList.remove('dark');
                            body.classList.remove('dark', 'bg-gray-900');
                        }
                    }
                });
            });
        </script>

        <!-- Apply dark mode immediately to prevent flash -->
        <script>
            (function() {
                const savedTheme = localStorage.getItem('theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = savedTheme || systemTheme;
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.body.classList.add('dark', 'bg-gray-900');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.body.classList.remove('dark', 'bg-gray-900');
                }
            })();
        </script>
    </head>
    
    <body class="font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100"
          x-data="{ loaded: true }">
        
        <!-- Preloader untuk Guest Layout -->
        <div x-show="!loaded" class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-[#465fff]"></div>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Memuat...</p>
            </div>
        </div>

        <div x-show="loaded" class="min-h-screen flex flex-row justify-center items-center bg-gray-100 dark:bg-gray-900">
            <div class="w-full px-6 py-4 overflow-hidden">
                {{ $slot }}
            </div>
        </div>

        <!-- Trigger preloader hide -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    document.querySelector('[x-data]').__x.$data.loaded = true;
                }, 500);
            });
        </script>
    </body>
</html>