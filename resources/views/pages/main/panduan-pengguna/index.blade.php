@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Panduan Pengguna" />

    {{-- ===== HEADER PENGANTAR ===== --}}
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xs mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div>
            <div>
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <div class="mb-1.5">
                            <span class="inline-flex items-center gap-2 rounded-lg border border-brand-100 bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-brand-700 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-300">
                                Sistem Monitoring Kinerja
                             </span>
                        </div>
                        <h1 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white md:text-3xl">
                            Pusat Panduan &amp; Dokumentasi Sistem
                        </h1>
                    </div>
                    <div class="shrink-0 flex items-center">
                        <img class="dark:hidden h-11 w-auto" src="/images/logo/logo.svg" alt="Pindang OI Logo" />
                        <img class="hidden dark:block h-11 w-auto" src="/images/logo/logo-dark.svg" alt="Pindang OI Logo" />
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                    Selamat datang di halaman bantuan Pindang OI. Halaman ini memuat alur kerja operasional, ketentuan validasi backend (hidden rules), logika perhitungan penilaian kinerja, serta panduan menggunakan fitur berdasarkan peran aktif Anda.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-brand-100 bg-brand-50/50 px-2.5 py-1 text-xs font-semibold text-brand-700 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-300">
                        <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Validasi Dinas Luar
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-100 bg-emerald-50/50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Cascading CKP
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-lg border border-purple-100 bg-purple-50/50 px-2.5 py-1 text-xs font-semibold text-purple-700 dark:border-purple-500/20 dark:bg-purple-500/10 dark:text-purple-300">
                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Perankingan Kinerja
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== DOKUMENTASI INTERAKTIF (ALPINE.JS) ===== --}}
    @if(count($availableTabs) > 0)
        <div x-data="{
            activeTab: '{{ $availableTabs[0] }}',
            activeSlug: '',
            guides: @js($groupedPanduans),
            routeMap: @js($routeMap),
            get currentGuides() {
                return this.guides[this.activeTab] || [];
            },
            get activeGuide() {
                return this.currentGuides.find(g => g.slug === this.activeSlug) || this.currentGuides[0] || null;
            },
            selectTab(tab) {
                this.activeTab = tab;
                const guides = this.currentGuides;
                this.activeSlug = guides.length > 0 ? guides[0].slug : '';
            },
            selectGuide(slug) {
                this.activeSlug = slug;
            },
            init() {
                this.selectTab(this.activeTab);
            }
        }" class="space-y-6">
            
            {{-- Tabs Selector --}}
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-800 pb-4">
                @foreach ($availableTabs as $tab)
                    <button
                        type="button"
                        @click="selectTab('{{ $tab }}')"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer border shadow-xs"
                        :class="activeTab === '{{ $tab }}'
                            ? 'bg-blue-600 border-blue-600 text-white dark:bg-blue-600 dark:border-blue-600 shadow-md shadow-blue-500/20'
                            : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200'"
                    >
                        Panduan {{ $tab }}
                    </button>
                @endforeach
            </div>

            {{-- Split Layout Sidebar + Content --}}
            <div class="flex flex-col lg:flex-row gap-6">
                
                {{-- Sidebar Kiri --}}
                <div class="w-full lg:w-72 shrink-0">
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs max-h-[500px] overflow-y-auto">
                        <div class="mb-3 px-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Daftar Fitur</span>
                        </div>
                        <nav class="space-y-1">
                            <template x-for="guide in currentGuides" :key="guide.slug">
                                <button
                                    @click="selectGuide(guide.slug)"
                                    class="w-full text-left px-3 py-2 rounded-xl text-xs transition-all duration-200 flex items-center justify-between group border"
                                    :class="activeSlug === guide.slug
                                        ? 'bg-blue-50/80 border-blue-100 text-blue-700 dark:bg-blue-950/20 dark:border-blue-900/40 dark:text-blue-400 font-bold'
                                        : 'bg-transparent border-transparent text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200'"
                                >
                                    <span x-text="guide.menu_name"></span>
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>

                {{-- Detail Konten Kanan --}}
                <div class="flex-1 min-w-0">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs min-h-[400px]">
                        <template x-if="activeGuide">
                            <div class="space-y-6">
                                {{-- Judul & Action Link --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="activeGuide.title"></h2>
                                        
                                        {{-- Badges Peran --}}
                                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Akses Oleh:</span>
                                            <template x-for="role in activeGuide.roles_allowed" :key="role">
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-950/30 dark:text-blue-300 dark:border-blue-900/30" x-text="role"></span>
                                            </template>
                                        </div>
                                    </div>

                                    <template x-if="activeGuide.route_target && routeMap[activeGuide.slug]">
                                        <div class="shrink-0">
                                            <a :href="routeMap[activeGuide.slug]" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-blue-600 rounded-full hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                Buka Halaman Fitur
                                            </a>
                                        </div>
                                    </template>
                                </div>

                                {{-- Penjelasan --}}
                                <div class="space-y-2">
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Deskripsi Fitur</h3>
                                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400" x-html="activeGuide.explanation"></p>
                                </div>

                                {{-- Langkah Penggunaan / Tutorial --}}
                                <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-100 dark:bg-gray-800/20 dark:border-gray-800">
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Langkah Penggunaan</h3>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed" x-html="activeGuide.tutorial"></div>
                                </div>

                                {{-- Form & Validation Details (Jika ada CRUD) --}}
                                <template x-if="activeGuide.form_details && activeGuide.form_details.length > 0">
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Detail Pengisian Form / Modal</h3>
                                        <div class="border border-gray-150 dark:border-gray-800 rounded-xl overflow-x-auto overflow-hidden shadow-xs">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Nama Field</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Tipe Input</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Aturan Pengisian</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Validasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                                    <template x-for="field in activeGuide.form_details" :key="field.field">
                                                        <tr>
                                                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white" x-text="field.field"></td>
                                                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400" x-text="field.type"></td>
                                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="field.rules"></td>
                                                            <td class="px-4 py-3 font-medium text-red-600 dark:text-red-400" x-text="field.validation"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                {{-- Output --}}
                                <template x-if="activeGuide.output">
                                    <div class="p-4 rounded-xl border border-emerald-100 bg-emerald-50/20 dark:border-emerald-950/30 dark:bg-emerald-950/10">
                                        <h3 class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider mb-1.5">Hasil Akhir (Output)</h3>
                                        <p class="text-sm text-emerald-900 dark:text-emerald-300" x-text="activeGuide.output"></p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!activeGuide">
                            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Tidak ada panduan yang tersedia untuk peran ini.</span>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data panduan pengguna.</p>
        </div>
    @endif
@endsection
