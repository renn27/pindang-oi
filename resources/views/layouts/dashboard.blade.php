<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | Pindang OI</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
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

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
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

<body x-data="{ 'loaded': true }" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader />
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            @include('layouts.app-header')
            <x-ui.web-push-guide />
            <!-- <div x-data="{ showFeatureBanner: true }" 
                 x-show="showFeatureBanner" x-transition
                 class="sticky top-[73px] z-40 mx-4 mt-4 md:mx-6 md:mt-6 rounded-xl border border-brand-500/30 bg-brand-50/80 p-4 shadow-sm backdrop-blur-md dark:border-brand-500/30 dark:bg-brand-900/40 font-medium text-brand-800 dark:text-brand-200">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100/80 text-brand-600 dark:bg-brand-800/50 dark:text-brand-400">
                            <svg class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm">
                            <strong class="font-bold">🎉 Update Baru!</strong> Fitur CKP dan Pengiriman/Penerimaan tugas bulanan sudah bisa diakses. 
                            <button @click="$dispatch('open-feature-modal')" class="inline-block mt-1 sm:mt-0 sm:ml-1 font-bold text-brand-700 underline decoration-brand-400 decoration-2 underline-offset-2 hover:text-brand-800 hover:decoration-brand-600 dark:text-brand-300 dark:decoration-brand-500 dark:hover:text-brand-200 transition-colors">
                                Lihat apa saja perubahannya
                            </button>
                        </p>
                    </div>
                    <button @click="showFeatureBanner = false" class="shrink-0 text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-200 focus:outline-none bg-brand-500/10 hover:bg-brand-500/20 rounded-lg p-2 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div> -->

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const messages = [];

                @if (session('success'))
                    messages.push({ type: 'success', message: @js(session('success')) });
                @endif
                
                @if (session('error'))
                    messages.push({ type: 'error', message: @js(session('error')) });
                @endif
                
                @if (session('info'))
                    messages.push({ type: 'info', message: @js(session('info')) });
                @endif

                if (messages.length > 1) {
                    SwalHelper.stacked(messages);
                } else if (messages.length === 1) {
                    const msg = messages[0];
                    if (msg.type === 'success') {
                        SwalHelper.success(msg.message);
                    } else if (msg.type === 'error') {
                        SwalHelper.error(msg.message);
                    } else if (msg.type === 'info') {
                        SwalHelper.info(msg.message);
                    }
                }
            });
        </script>
    @endpush

    <!-- MODAL PENGUMUMAN -->
    <x-ui.announcement-modal id="announcementModal" />

    <!-- MODAL FEATURE LAUNCH -->
    <x-ui.feature-launch-modal />

</body>
@stack('scripts')


</html>
