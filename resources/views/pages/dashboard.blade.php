@extends('layouts.dashboard')

@section('content')

<div
    x-data="{
        currentMonth: new Date().getMonth() + 1,
        currentYear: new Date().getFullYear(),
        selectedMonth: {{ request('month', now()->month) }},
        selectedYear: {{ request('year', now()->year) }},

        months: [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ],

        prevMonth(){
            if(this.selectedMonth === 1){
                this.selectedMonth = 12
                this.selectedYear--
            }else{
                this.selectedMonth--
            }
            window.location.href = `?month=${this.selectedMonth}&year=${this.selectedYear}`;
        },

        nextMonth(){
            if(this.selectedMonth === 12){
                this.selectedMonth = 1
                this.selectedYear++
            }else{
                this.selectedMonth++
            }
            window.location.href = `?month=${this.selectedMonth}&year=${this.selectedYear}`;
        },

        isCurrentMonth(){
            return this.selectedMonth === this.currentMonth && this.selectedYear === this.currentYear
        }
    }">
    {{-- ===== HEADER IDENTITAS ===== --}}
    <div
        class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100">
        @auth
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Selamat Datang</p>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                        {{ Auth::user()->nama_pegawai }}
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-300">Role</span>
                    <span
                        class="rounded-full bg-brand-100 px-3 py-1 text-sm font-semibold text-brand-700 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ Auth::user()->display_role }}
                    </span>
                </div>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-300">Belum login</p>
        @endauth
    </div>

    {{-- ===== FILTER BULAN ===== --}}
    <div class="mb-6">
        <div class="flex items-center justify-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">

            <button
                @click="prevMonth()"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800">
                ←
            </button>

            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                <span x-text="months[selectedMonth - 1]"></span>
                <span x-text="selectedYear"></span>
            </div>

            <button
                @click="nextMonth()"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800">
                →
            </button>

        </div>
    </div>

    @if (!auth()->user()->isSuperUser())
        @if (auth()->user()->isAnggotaTim() ||
                (auth()->user()->isKetuaTim() && auth()->user()->kegiatanYangDipimpin()->exists()))
            @auth
                @if (auth()->user()->isAnggotaTim())
                    <div class="mb-8">
                        <div
                            class="rounded-2xl border border-gray-200 p-6 dark:from-gray-800 dark:to-gray-900 bg-white dark:bg-gray-900 dark:border-gray-800">
                            <div class="mb-6 text-center">
                                <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">Rekap Penugasan untuk
                                    {{ Auth::user()->nama_pegawai }}</h2>
                            </div>

                            <div class="col-span-12 xl:col-span-5 space-y-6">
                                <x-dashboard.vis-total-penugasan-pegawai :totalpenugasanPegawai="app(\App\Services\DashboardAnalyticsService::class)->summaryPenugasanAnggota(
                                    Auth::user()->id_pegawai,
                                )" />
                            </div>
                        </div>
                    </div>

                    {{-- ===== DAFTAR PENUGASAN BELUM SELESAI SBG ANGGOTA ===== --}}
                    @if((isset($unfinishedBerjalanAsAnggota) && $unfinishedBerjalanAsAnggota->count() > 0) || (isset($unfinishedTerlewatAsAnggota) && $unfinishedTerlewatAsAnggota->count() > 0))
                    <div class="mb-8 p-5 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border border-yellow-200 dark:border-yellow-700/50 rounded-2xl shadow-sm relative overflow-hidden ring-1 ring-yellow-400 dark:ring-yellow-600" x-data="{ 
                        activeTabAnggota: (new URLSearchParams(window.location.search).has('anggota_terlewat_page') ? 'terlewat' : 'berjalan'),
                        async fetchTab(e, containerId) {
                            let link = e.target.closest('nav[role=\'navigation\'] a');
                            if (link && link.href) {
                                e.preventDefault();
                                let container = document.getElementById(containerId);
                                container.style.opacity = '0.5';
                                try {
                                    let res = await fetch(link.href);
                                    let text = await res.text();
                                    let doc = new DOMParser().parseFromString(text, 'text/html');
                                    container.innerHTML = doc.getElementById(containerId).innerHTML;
                                    window.history.pushState({}, '', link.href);
                                } finally {
                                    container.style.opacity = '1';
                                }
                            }
                        }
                    }">
                        <!-- Decorative accent border on the left -->
                        <div class="absolute top-0 left-0 w-2 h-full bg-yellow-400 dark:bg-yellow-500"></div>
                        
                        <div class="flex items-center gap-3 mb-5 ml-2">
                            <span class="p-2 bg-yellow-200 dark:bg-yellow-800 rounded-lg shadow-sm">
                                <svg class="w-6 h-6 text-yellow-700 dark:text-yellow-300 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <h3 class="text-xl font-extrabold text-yellow-700 dark:text-yellow-400 animate-pulse tracking-wide">TO DO LIST ANGGOTA</h3>
                            <span class="bg-orange-100 text-orange-800 text-xs font-bold px-3 py-1 rounded-full border border-orange-200 shadow-sm dark:bg-orange-900 dark:text-orange-300 dark:border-orange-700">Harus Diselesaikan!</span>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700 mb-4">
                            <button @click="activeTabAnggota = 'berjalan'"
                                :class="{'border-blue-500 text-blue-600 dark:text-blue-500 dark:border-blue-500': activeTabAnggota === 'berjalan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabAnggota !== 'berjalan'}"
                                class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center">
                                Sedang Berjalan
                                @if(isset($unfinishedBerjalanAsAnggota) && $unfinishedBerjalanAsAnggota->total() > 0)
                                    <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs dark:bg-blue-900 dark:text-blue-200">{{ $unfinishedBerjalanAsAnggota->total() }}</span>
                                @endif
                            </button>
                            <button @click="activeTabAnggota = 'terlewat'"
                                :class="{'border-red-500 text-red-600 dark:text-red-500 dark:border-red-500': activeTabAnggota === 'terlewat', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabAnggota !== 'terlewat'}"
                                class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center">
                                Sudah Terlewat
                                @if(isset($unfinishedTerlewatAsAnggota) && $unfinishedTerlewatAsAnggota->total() > 0)
                                    <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs dark:bg-red-900 dark:text-red-200">{{ $unfinishedTerlewatAsAnggota->total() }}</span>
                                @endif
                            </button>
                        </div>

                        <!-- Tab Content -->
                        <div>
                            <div id="tab-anggota-berjalan" @click="fetchTab($event, 'tab-anggota-berjalan')" x-show="activeTabAnggota === 'berjalan'" style="display: none;" class="transition-opacity duration-200">
                                @if(isset($unfinishedBerjalanAsAnggota) && $unfinishedBerjalanAsAnggota->count() > 0)
                                    <x-tables.dashboard-penugasan-anggota :penugasans="$unfinishedBerjalanAsAnggota" />
                                @else
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400">Tidak ada penugasan yang sedang berjalan.</div>
                                @endif
                            </div>

                            <div id="tab-anggota-terlewat" @click="fetchTab($event, 'tab-anggota-terlewat')" x-show="activeTabAnggota === 'terlewat'" style="display: none;" x-cloak class="transition-opacity duration-200">
                                @if(isset($unfinishedTerlewatAsAnggota) && $unfinishedTerlewatAsAnggota->count() > 0)
                                    <x-tables.dashboard-penugasan-anggota :penugasans="$unfinishedTerlewatAsAnggota" />
                                @else
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400">Tidak ada penugasan yang sudah terlewat.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            @endauth

            @auth
                @if (auth()->user()->isKetuaTim())
                    <div class="mb-8">
                        <div
                            class="rounded-2xl border border-gray-200 p-6 dark:from-gray-800 dark:to-gray-900 bg-white dark:bg-gray-900 dark:border-gray-800">
                            <div class="mb-6 text-center">
                                <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">Rekap Kegiatan milik
                                    {{ Auth::user()->nama_pegawai }}</h2>
                            </div>

                            <div class="col-span-12 xl:col-span-5 space-y-6">
                                <x-dashboard.vis-total-kegiatan-ketua :totalkegiatanKetua="app(\App\Services\DashboardAnalyticsService::class)->summaryKegiatanKetua(
                                    Auth::user()->id_pegawai,
                                )" />
                            </div>
                        </div>
                    </div>

                    {{-- ===== DAFTAR PENUGASAN BELUM SELESAI SBG KETUA ===== --}}
                    @if((isset($unfinishedBerjalanAsKetua) && $unfinishedBerjalanAsKetua->count() > 0) || (isset($unfinishedTerlewatAsKetua) && $unfinishedTerlewatAsKetua->count() > 0))
                    <div class="mb-8 p-5 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-700/50 rounded-2xl shadow-sm relative overflow-hidden ring-1 ring-amber-400 dark:ring-amber-600" x-data="{ 
                        activeTabKetua: (new URLSearchParams(window.location.search).has('ketua_terlewat_page') ? 'terlewat' : 'berjalan'),
                        async fetchTab(e, containerId) {
                            let link = e.target.closest('nav[role=\'navigation\'] a');
                            if (link && link.href) {
                                e.preventDefault();
                                let container = document.getElementById(containerId);
                                container.style.opacity = '0.5';
                                try {
                                    let res = await fetch(link.href);
                                    let text = await res.text();
                                    let doc = new DOMParser().parseFromString(text, 'text/html');
                                    container.innerHTML = doc.getElementById(containerId).innerHTML;
                                    window.history.pushState({}, '', link.href);
                                } finally {
                                    container.style.opacity = '1';
                                }
                            }
                        }
                    }">
                        <div class="absolute top-0 left-0 w-2 h-full bg-amber-400 dark:bg-amber-500"></div>

                        <div class="flex items-center gap-3 mb-5 ml-2">
                            <span class="p-2 bg-amber-200 dark:bg-amber-800 rounded-lg shadow-sm">
                                <svg class="w-6 h-6 text-amber-700 dark:text-amber-300 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <h3 class="text-xl font-extrabold text-amber-700 dark:text-amber-400 animate-pulse tracking-wide">TO DO LIST KETUA TIM</h3>
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 shadow-sm dark:bg-amber-900 dark:text-amber-300 dark:border-amber-700">Wajib Diperiksa!</span>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700 mb-4">
                            <button @click="activeTabKetua = 'berjalan'"
                                :class="{'border-blue-500 text-blue-600 dark:text-blue-500 dark:border-blue-500': activeTabKetua === 'berjalan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabKetua !== 'berjalan'}"
                                class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center">
                                Sedang Berjalan
                                @if(isset($unfinishedBerjalanAsKetua) && $unfinishedBerjalanAsKetua->total() > 0)
                                    <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs dark:bg-blue-900 dark:text-blue-200">{{ $unfinishedBerjalanAsKetua->total() }}</span>
                                @endif
                            </button>
                            <button @click="activeTabKetua = 'terlewat'"
                                :class="{'border-red-500 text-red-600 dark:text-red-500 dark:border-red-500': activeTabKetua === 'terlewat', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabKetua !== 'terlewat'}"
                                class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center">
                                Sudah Terlewat
                                @if(isset($unfinishedTerlewatAsKetua) && $unfinishedTerlewatAsKetua->total() > 0)
                                    <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs dark:bg-red-900 dark:text-red-200">{{ $unfinishedTerlewatAsKetua->total() }}</span>
                                @endif
                            </button>
                        </div>

                        <!-- Tab Content -->
                        <div>
                            <div id="tab-ketua-berjalan" @click="fetchTab($event, 'tab-ketua-berjalan')" x-show="activeTabKetua === 'berjalan'" style="display: none;" class="transition-opacity duration-200">
                                @if(isset($unfinishedBerjalanAsKetua) && $unfinishedBerjalanAsKetua->count() > 0)
                                    <x-tables.dashboard-penugasan-ketua :penugasans="$unfinishedBerjalanAsKetua" />
                                @else
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400">Tidak ada penugasan yang sedang berjalan.</div>
                                @endif
                            </div>

                            <div id="tab-ketua-terlewat" @click="fetchTab($event, 'tab-ketua-terlewat')" x-show="activeTabKetua === 'terlewat'" style="display: none;" x-cloak class="transition-opacity duration-200">
                                @if(isset($unfinishedTerlewatAsKetua) && $unfinishedTerlewatAsKetua->count() > 0)
                                    <x-tables.dashboard-penugasan-ketua :penugasans="$unfinishedTerlewatAsKetua" />
                                @else
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400">Tidak ada penugasan yang sudah terlewat.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            @endauth
        @elseif (auth()->user()->isKetuaTim())
            <div class="mb-8">
                <div
                    class="rounded-2xl border border-dashed border-gray-300 p-6 bg-gray-50 dark:bg-gray-900 dark:border-gray-700">

                    <div class="text-center">
                        <h2 class="mb-2 text-xl font-semibold text-gray-700 dark:text-gray-300 italic">
                            Belum Ada Kegiatan yang Dibuat
                        </h2>

                        <p class="text-gray-500 dark:text-gray-400">
                            Saat ini Anda belum membuat kegiatan apapun. Silakan buat kegiatan baru untuk mulai mengelola
                            penugasan bersama Anggota Tim Anda.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8">
                <div
                    class="rounded-2xl border border-dashed border-gray-300 p-6 bg-gray-50 dark:bg-gray-900 dark:border-gray-700">

                    <div class="text-center">
                        <h2 class="mb-2 text-xl font-semibold text-gray-700 dark:text-gray-300 italic">
                            Belum Termasuk Dalam Penugasan
                        </h2>

                        <p class="text-gray-500 dark:text-gray-400">
                            Saat ini Anda belum termasuk dalam penugasan pada bidang manapun.
                            Silakan menunggu penugasan dari Ketua Tim atau hubungi atasan Anda
                            untuk mendapatkan informasi lebih lanjut.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @else()
        {{-- ===== CARD BESAR ANALYTICS DASHBOARD ===== --}}
        <div class="mb-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">

                @php
                    $hasData = $stats['total_kegiatan'] > 0 || $stats['total_sub_kegiatan'] > 0 || $stats['total_penugasan'] > 0;
                @endphp

                {{-- jika tidak ada data sama sekali --}}
                <template x-if="!{{ $hasData ? 'true' : 'false' }}">
                    <div class="py-20 text-center">
                        <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-2">
                            Belum Ada Statistik
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400">
                            Belum ada Statistik Kegiatan, Best Employee, dan Rank Employee untuk bulan ini.
                        </p>
                    </div>
                </template>

                {{-- jika ada data --}}
                <div x-show="{{ $hasData ? 'true' : 'false' }}">

                    {{-- ===== SECTION STATISTIK KEGIATAN ===== --}}
                    <div class="mb-10">

                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Statistik Kegiatan</h3>
                            <div class="rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                <span x-text="months[selectedMonth - 1]"></span>
                                <span x-text="selectedYear"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- TOTAL KEGIATAN --}}
                            <div
                                class="rounded-xl border border-gray-100 bg-gradient-to-br from-white to-blue-50 p-5 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-blue-900/20">
                                <div class="flex flex-col items-center text-center">
                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40">
                                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Total Kegiatan</p>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                        {{ number_format($stats['total_kegiatan'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- TOTAL SUB KEGIATAN --}}
                            <div
                                class="rounded-xl border border-gray-100 bg-gradient-to-br from-white to-purple-50 p-5 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-purple-900/20">
                                <div class="flex flex-col items-center text-center">
                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/40">
                                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Total Sub Kegiatan</p>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                        {{ number_format($stats['total_sub_kegiatan'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- TOTAL PENUGASAN --}}
                            <div
                                class="rounded-xl border border-gray-100 bg-gradient-to-br from-white to-amber-50 p-5 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-amber-900/20">
                                <div class="flex flex-col items-center text-center">
                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                        </svg>
                                    </div>
                                    <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Total Penugasan</p>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                        {{ number_format($stats['total_penugasan'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- PERSENTASE SELESAI --}}
                            <div
                                class="rounded-xl border border-gray-100 bg-gradient-to-br from-white to-green-50 p-5 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-green-900/20 relative">
                                <div class="flex flex-col items-center text-center">
                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40">
                                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Persentase Selesai</p>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                        {{ $stats['persentase_selesai'] }}%
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-6">
                            <div class="mb-2 flex justify-between text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span>Progress Penugasan</span>
                                <span>{{ $stats['persentase_selesai'] }}%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-green-400 via-green-500 to-green-600 transition-all duration-700"
                                    style="width: {{ $stats['persentase_selesai'] }}%"></div>
                            </div>
                            <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $stats['penugasan_selesai'] }} Selesai</span>
                                <span>{{ $stats['penugasan_berjalan'] }} Belum Selesai</span>
                            </div>
                        </div>

                    </div>

                    {{-- ===== BEST EMPLOYEE ===== --}}
                    <div class="mb-10">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Best Employee</h3>
                            <div class="rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                <span x-text="months[selectedMonth - 1]"></span>
                                <span x-text="selectedYear"></span>
                            </div>
                        </div>

                        <x-profile.employe-rank-card
                            :bestEmployee="$bestEmployee" />
                    </div>

                    {{-- ===== RANK EMPLOYEE ===== --}}
                    <div id="container-rank-pegawai" x-data="{
                        async fetchTab(e, containerId) {
                            let link = e.target.closest('a');
                            if (link && link.href) {
                                e.preventDefault();
                                let container = document.getElementById(containerId);
                                container.style.opacity = '0.5';
                                try {
                                    let res = await fetch(link.href);
                                    let text = await res.text();
                                    let doc = new DOMParser().parseFromString(text, 'text/html');
                                    container.innerHTML = doc.getElementById(containerId).innerHTML;
                                    window.history.pushState({}, '', link.href);
                                } finally {
                                    container.style.opacity = '1';
                                }
                            }
                        }
                    }" @click="fetchTab($event, 'container-rank-pegawai')" class="transition-opacity duration-200">
                        @auth
                            <x-dashboard.vis-rank-pegawai
                                :rankPegawai="$rankPegawai"
                                :perPage="5" />
                        @endauth
                    </div>

                </div>

            </div>
        </div>

    @endif

    @if(auth()->user()->isSuperUser())
        <div class="mb-8">
            <x-dashboard.vis-rekap-penugasan-pegawai />
        </div>
    @endif

</div>

@endsection
