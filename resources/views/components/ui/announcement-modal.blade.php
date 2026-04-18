@props([
    'id' => 'announcementModal',
    'showCloseButton' => true,
])

<div x-data="{
    modalId: '{{ $id }}',
    open: false,
    currentIndex: 0,
    announcements: [],
    isLoading: true,

    async fetchAnnouncements() {
        try {
            const response = await fetch('/api/active-announcements');
            const data = await response.json();
            this.announcements = data;

            if (data.length > 0) {
                // Cek apakah ada pengumuman baru (yang belum pernah dilihat)
                const hasNewAnnouncement = this.checkForNewAnnouncements(data);

                if (hasNewAnnouncement) {
                    // Ada pengumuman baru, tampilkan modal
                    setTimeout(() => { this.open = true; }, 500);
                } else {
                    // Tidak ada pengumuman baru, cek waktu terakhir tampil
                    const lastShown = localStorage.getItem('announcement_modal_shown');
                    const now = new Date().getTime();
                    const SIX_HOURS = 6 * 60 * 60 * 1000; // 6 jam

                    if (!lastShown || (now - parseInt(lastShown) > SIX_HOURS)) {
                        setTimeout(() => { this.open = true; }, 500);
                    }
                }
            }
        } catch (error) {
            console.error('Error loading announcements:', error);
        } finally {
            this.isLoading = false;
        }
    },

    checkForNewAnnouncements(currentAnnouncements) {
        // Ambil daftar ID pengumuman yang sudah pernah dilihat
        const seenAnnouncements = JSON.parse(localStorage.getItem('seen_announcements') || '[]');

        // Cek apakah ada pengumuman yang belum pernah dilihat
        const newAnnouncements = currentAnnouncements.filter(a => !seenAnnouncements.includes(a.id));

        console.log('Seen:', seenAnnouncements);
        console.log('Current:', currentAnnouncements.map(a => a.id));
        console.log('New:', newAnnouncements.map(a => a.id));

        return newAnnouncements.length > 0;
    },

    markAllAnnouncementsAsSeen() {
        if (this.announcements.length > 0) {
            const seenAnnouncements = JSON.parse(localStorage.getItem('seen_announcements') || '[]');

            // Tambahkan semua ID pengumuman yang aktif ke daftar seen
            this.announcements.forEach(announcement => {
                if (announcement.id && !seenAnnouncements.includes(announcement.id)) {
                    seenAnnouncements.push(announcement.id);
                }
            });

            // Simpan kembali ke localStorage (maksimal 50 ID terakhir)
            const trimmedSeen = seenAnnouncements.slice(-50);
            localStorage.setItem('seen_announcements', JSON.stringify(trimmedSeen));

            console.log('Marked as seen:', trimmedSeen);
        }
    },

    init() {
        this.fetchAnnouncements();

        this.$watch('open', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'unset';
            }
        });
    },

    closeModal() {
        // Tandai semua pengumuman sebagai sudah dilihat SAAT MODAL DITUTUP
        this.markAllAnnouncementsAsSeen();

        // Simpan waktu close
        localStorage.setItem('announcement_modal_shown', new Date().getTime());

        this.open = false;
    },

    nextAnnouncement() {
        if (this.currentIndex < this.announcements.length - 1) {
            this.currentIndex++;
        }
    },

    prevAnnouncement() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
        }
    }
}" x-show="open" x-cloak @keydown.escape.window="closeModal()"
    class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-hidden p-4" x-transition.duration.300ms
    {{ $attributes }}>

    <!-- Backdrop -->
    <div @click="closeModal()" class="fixed inset-0 h-full w-full bg-gray-900/50 backdrop-blur-sm dark:bg-black/70"
        x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal Content Container -->
    <div class="relative flex items-center justify-center"
        :class="{
            'h-[90vh] w-[90vw]': !announcements[currentIndex]?.image,
            'h-[85vh] w-[70vw] lg:h-[90vh] lg:w-[60vw]': announcements[currentIndex]?.image
        }">

        <!-- Modal Content dengan Animasi Slide dari Atas -->
        <div @click.stop
            class="relative flex h-full w-full flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900 dark:ring-1 dark:ring-gray-800"
            x-show="open" x-transition:enter="transition-all duration-500 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-24 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition-all duration-300 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-12 scale-95">

            <!-- Header -->
            <div class="relative shrink-0 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/30">
                        <svg class="h-4 w-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white"
                            x-text="announcements[currentIndex]?.title || 'Pengumuman'"></h3>
                    </div>
                </div>

                @if ($showCloseButton)
                    <button @click="closeModal()"
                        class="absolute right-2 top-2 flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600 transition-all hover:bg-gray-300 hover:text-gray-800 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Body -->
            <div class="flex flex-1 flex-col overflow-hidden p-4">
                <!-- Loading -->
                <template x-if="isLoading">
                    <div class="flex h-full items-center justify-center">
                        <div class="text-gray-500">Memuat pengumuman...</div>
                    </div>
                </template>

                <!-- Ada data -->
                <template x-if="!isLoading && announcements.length > 0">
                    <div class="flex h-full flex-col">
                        <div x-show="announcements[currentIndex].image"
                            class="relative mb-3 flex min-h-0 flex-1 items-center justify-center overflow-hidden rounded-xl">
                            <img :src="announcements[currentIndex].image" :alt="announcements[currentIndex].title"
                                class="h-full w-full object-contain">
                        </div>

                        <div class="shrink-0 overflow-y-auto pr-1"
                            :class="{ 'max-h-[20%]': announcements[currentIndex].image, 'h-full': !announcements[currentIndex]
                                    .image }">
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                <div class="text-sm text-gray-600 dark:text-gray-300 prose dark:prose-invert max-w-none"
                                    x-html="announcements[currentIndex].content"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Tidak ada data -->
                <template x-if="!isLoading && announcements.length === 0">
                    <div class="flex h-full items-center justify-center">
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada pengumuman saat ini</p>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="shrink-0 border-t border-gray-200 px-4 py-2 dark:border-gray-700"
                x-show="announcements.length > 0">
                <div class="flex items-center justify-between">
                    <div class="flex gap-1">
                        <template x-for="(announcement, index) in announcements" :key="index">
                            <button @click="currentIndex = index" class="h-1.5 rounded-full transition-all duration-200"
                                :class="index === currentIndex ?
                                    'w-5 bg-brand-500 dark:bg-brand-400' :
                                    'w-1.5 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500'">
                            </button>
                        </template>
                    </div>

                    <span
                        class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        <span x-text="currentIndex + 1"></span>/<span x-text="announcements.length"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button @click="prevAnnouncement()" x-show="currentIndex > 0"
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            class="absolute -left-6 z-20 hidden -translate-x-full rounded-full bg-white p-3 shadow-lg transition-all hover:scale-110 hover:bg-gray-50 hover:shadow-xl dark:bg-gray-800 dark:hover:bg-gray-700 lg:block">
            <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button @click="nextAnnouncement()" x-show="currentIndex < announcements.length - 1"
            x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="absolute -right-6 z-20 hidden translate-x-full rounded-full bg-white p-3 shadow-lg transition-all hover:scale-110 hover:bg-gray-50 hover:shadow-xl dark:bg-gray-800 dark:hover:bg-gray-700 lg:block">
            <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Mobile Arrows -->
        <button @click="prevAnnouncement()" x-show="currentIndex > 0"
            class="absolute left-2 z-20 rounded-full bg-white/90 p-2.5 shadow-md backdrop-blur-sm transition-all hover:scale-110 hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-800 lg:hidden">
            <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button @click="nextAnnouncement()" x-show="currentIndex < announcements.length - 1"
            class="absolute right-2 z-20 rounded-full bg-white/90 p-2.5 shadow-md backdrop-blur-sm transition-all hover:scale-110 hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-800 lg:hidden">
            <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>

<style>
    [x-cloak] {
        display: none;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #475569;
    }
</style>
