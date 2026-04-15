{{-- resources/views/pages/main/announcements/index.blade.php --}}
@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pengumuman</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Informasi dan pengumuman terbaru untuk seluruh pegawai
        </p>
    </div>

    <!-- Tabs -->
    <div x-data="{ 
        activeTab: 'active',
        selectedAnnouncement: null,
        showDetailModal: false,
        currentIndex: 0,
        filteredAnnouncements: [],
        
        init() {
            this.filterAnnouncements();
            this.$watch('activeTab', () => this.filterAnnouncements());
        },
        
        filterAnnouncements() {
            const allAnnouncements = {{ Js::from($announcements->map(function($item) {
                $isActive = $item->is_active && $item->start_date->isPast() && $item->end_date->isFuture();
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'image_url' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                    'start_date' => $item->start_date->format('d M Y'),
                    'end_date' => $item->end_date->format('d M Y'),
                    'is_active' => $isActive
                ];
            })) }};
            
            if (this.activeTab === 'active') {
                this.filteredAnnouncements = allAnnouncements.filter(a => a.is_active);
            } else if (this.activeTab === 'inactive') {
                this.filteredAnnouncements = allAnnouncements.filter(a => !a.is_active);
            } else {
                this.filteredAnnouncements = allAnnouncements;
            }
        },
        
        openDetail(announcement, index) {
            this.selectedAnnouncement = announcement;
            this.currentIndex = index;
            this.showDetailModal = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeDetail() {
            this.showDetailModal = false;
            this.selectedAnnouncement = null;
            document.body.style.overflow = 'unset';
        },
        
        nextAnnouncement() {
            if (this.currentIndex < this.filteredAnnouncements.length - 1) {
                this.currentIndex++;
                this.selectedAnnouncement = this.filteredAnnouncements[this.currentIndex];
            }
        },
        
        prevAnnouncement() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.selectedAnnouncement = this.filteredAnnouncements[this.currentIndex];
            }
        }
    }">
        
        <!-- Filter Tabs -->
        <div class="mb-6">
            <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl w-fit dark:bg-gray-800">
                <button @click="activeTab = 'active'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                    :class="activeTab === 'active' 
                        ? 'bg-white text-brand-600 shadow-sm dark:bg-gray-700 dark:text-brand-400' 
                        : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'">
                    Aktif
                </button>
                <button @click="activeTab = 'inactive'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                    :class="activeTab === 'inactive' 
                        ? 'bg-white text-brand-600 shadow-sm dark:bg-gray-700 dark:text-brand-400' 
                        : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'">
                    Nonaktif
                </button>
                <button @click="activeTab = 'all'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                    :class="activeTab === 'all' 
                        ? 'bg-white text-brand-600 shadow-sm dark:bg-gray-700 dark:text-brand-400' 
                        : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'">
                    Semua
                </button>
            </div>
        </div>

        <!-- Card Grid -->
        <div>
            <template x-if="filteredAnnouncements.length > 0">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <template x-for="(announcement, index) in filteredAnnouncements" :key="announcement.id">
                        <div @click="openDetail(announcement, index)"
                            class="group cursor-pointer overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                            
                            <!-- Image -->
                            <div class="relative h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <template x-if="announcement.image_url">
                                    <img :src="announcement.image_url" 
                                         :alt="announcement.title"
                                         class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                </template>
                                <template x-if="!announcement.image_url">
                                    <div class="flex h-full items-center justify-center">
                                        <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                                
                                <!-- Status Badge -->
                                <div class="absolute left-3 top-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium shadow-sm"
                                        :class="announcement.is_active 
                                            ? 'bg-green-500 text-white' 
                                            : 'bg-gray-500 text-white'"
                                        x-text="announcement.is_active ? 'Aktif' : 'Nonaktif'">
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-4">
                                <h3 class="mb-2 line-clamp-2 text-base font-semibold text-gray-800 group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400"
                                    x-text="announcement.title"></h3>
                                
                                <p class="mb-3 line-clamp-3 text-sm text-gray-600 dark:text-gray-400"
                                    x-text="announcement.content.substring(0, 100) + '...'"></p>
                                
                                <!-- Date Info -->
                                <div class="flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-700">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span x-text="announcement.start_date"></span>
                                    </div>
                                    
                                    <span class="text-xs text-brand-600 opacity-0 transition-opacity group-hover:opacity-100 dark:text-brand-400">
                                        Lihat Detail →
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            
            <template x-if="filteredAnnouncements.length === 0">
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400" x-text="activeTab === 'active' ? 'Tidak ada pengumuman aktif' : (activeTab === 'inactive' ? 'Tidak ada pengumuman nonaktif' : 'Belum ada pengumuman')"></p>
                </div>
            </template>
        </div>

        <!-- Detail Modal menggunakan komponen announcement-modal -->
        <div x-show="showDetailModal" x-cloak>
            <div class="modal fixed inset-0 z-99999 flex items-center justify-center overflow-hidden p-4"
                 x-transition.duration.300ms>
                
                <!-- Backdrop -->
                <div @click="closeDetail()" 
                    class="fixed inset-0 h-full w-full bg-gray-900/50 backdrop-blur-sm dark:bg-black/70"
                    x-show="showDetailModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                </div>

                <!-- Modal Content Container -->
                <div class="relative flex items-center justify-center"
                     :class="{
                        'h-[90vh] w-[90vw]': !selectedAnnouncement?.image_url,
                        'h-[85vh] w-[70vw] lg:h-[90vh] lg:w-[60vw]': selectedAnnouncement?.image_url
                     }">
                    
                    <!-- Modal Content -->
                    <div @click.stop 
                        class="relative flex h-full w-full flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900 dark:ring-1 dark:ring-gray-800"
                        x-show="showDetailModal"
                        x-transition:enter="transition-all duration-500 ease-out"
                        x-transition:enter-start="opacity-0 -translate-y-24 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition-all duration-300 ease-in"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-12 scale-95">

                        <!-- Header -->
                        <div class="relative shrink-0 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/30">
                                    <svg class="h-4 w-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white" 
                                        x-text="selectedAnnouncement?.title || 'Pengumuman'"></h3>
                                </div>
                            </div>
                            
                            <button @click="closeDetail()"
                                class="absolute right-2 top-2 flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600 transition-all hover:bg-gray-300 hover:text-gray-800 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="flex flex-1 flex-col overflow-hidden p-4">
                            <div class="flex h-full flex-col">
                                <div x-show="selectedAnnouncement?.image_url" 
                                     class="relative mb-3 flex min-h-0 flex-1 items-center justify-center overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
                                    <img :src="selectedAnnouncement?.image_url" 
                                         :alt="selectedAnnouncement?.title"
                                         class="h-full w-full object-contain">
                                </div>
                                
                                <!-- Date Info -->
                                <div class="mb-3 flex items-center gap-4 text-sm">
                                    <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span x-text="selectedAnnouncement?.start_date"></span>
                                        <span class="mx-1">-</span>
                                        <span x-text="selectedAnnouncement?.end_date"></span>
                                    </div>
                                    
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          :class="selectedAnnouncement?.is_active 
                                              ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                                              : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400'"
                                          x-text="selectedAnnouncement?.is_active ? 'Aktif' : 'Nonaktif'">
                                    </span>
                                </div>
                                
                                <div class="shrink-0 overflow-y-auto pr-1" 
                                     :class="{ 'max-h-[30%]': selectedAnnouncement?.image_url, 'h-full': !selectedAnnouncement?.image_url }">
                                    <div class="prose prose-sm max-w-none dark:prose-invert">
                                        <p class="whitespace-pre-line text-sm text-gray-600 dark:text-gray-300" 
                                           x-text="selectedAnnouncement?.content"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="shrink-0 border-t border-gray-200 px-4 py-2 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex gap-1">
                                    <template x-for="(ann, idx) in filteredAnnouncements" :key="ann.id">
                                        <button @click="currentIndex = idx; selectedAnnouncement = ann"
                                            class="h-1.5 rounded-full transition-all duration-200"
                                            :class="idx === currentIndex 
                                                ? 'w-5 bg-brand-500 dark:bg-brand-400' 
                                                : 'w-1.5 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500'">
                                        </button>
                                    </template>
                                </div>
                                
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    <span x-text="currentIndex + 1"></span>/<span x-text="filteredAnnouncements.length"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <button @click="prevAnnouncement()"
                        x-show="currentIndex > 0"
                        x-transition:enter="transition-all duration-300 ease-out"
                        x-transition:enter-start="opacity-0 -translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        class="absolute -left-6 z-20 hidden -translate-x-full rounded-full bg-white p-3 shadow-lg transition-all hover:scale-110 hover:bg-gray-50 hover:shadow-xl dark:bg-gray-800 dark:hover:bg-gray-700 lg:block">
                        <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <button @click="nextAnnouncement()"
                        x-show="currentIndex < filteredAnnouncements.length - 1"
                        x-transition:enter="transition-all duration-300 ease-out"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        class="absolute -right-6 z-20 hidden translate-x-full rounded-full bg-white p-3 shadow-lg transition-all hover:scale-110 hover:bg-gray-50 hover:shadow-xl dark:bg-gray-800 dark:hover:bg-gray-700 lg:block">
                        <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    
                    <!-- Mobile Arrows -->
                    <button @click="prevAnnouncement()"
                        x-show="currentIndex > 0"
                        class="absolute left-2 z-20 rounded-full bg-white/90 p-2.5 shadow-md backdrop-blur-sm transition-all hover:scale-110 hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-800 lg:hidden">
                        <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <button @click="nextAnnouncement()"
                        x-show="currentIndex < filteredAnnouncements.length - 1"
                        class="absolute right-2 z-20 rounded-full bg-white/90 p-2.5 shadow-md backdrop-blur-sm transition-all hover:scale-110 hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-800 lg:hidden">
                        <svg class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .overflow-y-auto::-webkit-scrollbar { width: 4px; }
        .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
        .overflow-y-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark .overflow-y-auto::-webkit-scrollbar-thumb { background: #475569; }
    </style>
@endsection