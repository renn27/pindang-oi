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
    }"
>

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
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
            >
                ←
            </button>

            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                <span x-text="months[selectedMonth - 1]"></span>
                <span x-text="selectedYear"></span>
            </div>

            <button
                @click="nextMonth()"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
            >
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
                                <x-profile.vis-total-penugasan-pegawai :totalpenugasanPegawai="app(\App\Services\DashboardAnalyticsService::class)->summaryPenugasanAnggota(
                                    Auth::user()->id_pegawai,
                                )" />
                            </div>
                        </div>
                    </div>

                    {{-- ===== DAFTAR PENUGASAN BELUM SELESAI SBG ANGGOTA ===== --}}
                    @if(isset($unfinishedAsAnggota) && $unfinishedAsAnggota->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Penugasan Belum Selesai (Sebagai Anggota)</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">Anggota Tim</span>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 dark:text-gray-400">No.</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Bidang & Kegiatan</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Anggota</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">Target</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40 dark:text-gray-400">Status Penugasannya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                        @foreach ($unfinishedAsAnggota as $index => $penugasan)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 cursor-pointer"
                                                onclick="window.location.href='{{ route('sub.kegiatan.show', ['kegiatan' => $penugasan->subKegiatan->id_kegiatan, 'subKegiatan' => $penugasan->id_sub_kegiatan]) }}#penugasan-{{ $penugasan->id_penugasan }}'">
                                                <td class="px-4 py-4 text-sm text-gray-600 font-medium text-center dark:text-gray-400">
                                                    {{ $unfinishedAsAnggota->firstItem() + $index }}
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="flex flex-col gap-1">
                                                        <span class="text-xs text-brand-600 font-medium dark:text-brand-400 line-clamp-1">
                                                            {{ $penugasan->subKegiatan->kegiatan->bidang->nama_bidang ?? '-' }}
                                                        </span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 p-0 w-80 md:w-full break-words">
                                                            {{ $penugasan->subKegiatan->kegiatan->nama_rk_kegiatan ?? '-' }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 font-medium dark:text-gray-400 line-clamp-2">
                                                            Sub: {{ $penugasan->subKegiatan->nama_sub_kegiatan ?? '-' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                                        {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="inline-flex py-1 px-3 rounded-md bg-gray-100 text-gray-800 text-xs font-bold dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                                        {{ $penugasan->target }} {{ $penugasan->satuan_target }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusInfo = $penugasan->statusPenugasan();
                                                    @endphp
                                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusInfo['class'] ?? 'bg-gray-100 text-gray-600' }}">
                                                        {{ $statusInfo['label'] ?? 'Tidak Diketahui' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                {{ $unfinishedAsAnggota->links() }}
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
                                <x-profile.vis-total-kegiatan-ketua :totalkegiatanKetua="app(\App\Services\DashboardAnalyticsService::class)->summaryKegiatanKetua(
                                    Auth::user()->id_pegawai,
                                )" />
                            </div>
                        </div>
                    </div>

                    {{-- ===== DAFTAR PENUGASAN BELUM SELESAI SBG KETUA ===== --}}
                    @if(isset($unfinishedAsKetua) && $unfinishedAsKetua->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Penugasan Anggota Belum Selesai (Sebagai Ketua Tim)</h3>
                            <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-amber-200 dark:text-amber-800">Ketua Tim</span>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 dark:text-gray-400">No.</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Bidang & Kegiatan</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Anggota</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">Target</th>
                                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40 dark:text-gray-400">Status Penugasannya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                        @foreach ($unfinishedAsKetua as $index => $penugasan)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 cursor-pointer"
                                                onclick="window.location.href='{{ route('sub.kegiatan.show', ['kegiatan' => $penugasan->subKegiatan->id_kegiatan, 'subKegiatan' => $penugasan->id_sub_kegiatan]) }}#penugasan-{{ $penugasan->id_penugasan }}'">
                                                <td class="px-4 py-4 text-sm text-gray-600 font-medium text-center dark:text-gray-400">
                                                    {{ $unfinishedAsKetua->firstItem() + $index }}
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="flex flex-col gap-1">
                                                        <span class="text-xs text-brand-600 font-medium dark:text-brand-400 line-clamp-1">
                                                            {{ $penugasan->subKegiatan->kegiatan->bidang->nama_bidang ?? '-' }}
                                                        </span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 p-0 w-80 md:w-full break-words">
                                                            {{ $penugasan->subKegiatan->kegiatan->nama_rk_kegiatan ?? '-' }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 font-medium dark:text-gray-400 line-clamp-2">
                                                            Sub: {{ $penugasan->subKegiatan->nama_sub_kegiatan ?? '-' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                                        {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="inline-flex py-1 px-3 rounded-md bg-gray-100 text-gray-800 text-xs font-bold dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                                        {{ $penugasan->target }} {{ $penugasan->satuan_target }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusInfo = $penugasan->statusPenugasan();
                                                    @endphp
                                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusInfo['class'] ?? 'bg-gray-100 text-gray-600' }}">
                                                        {{ $statusInfo['label'] ?? 'Tidak Diketahui' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                {{ $unfinishedAsKetua->links() }}
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
                    $selectedMonth = request('month', now()->month);
                    $selectedYear = request('year', now()->year);
                    $stats = app(\App\Services\DashboardAnalyticsService::class)->getDashboardStats($selectedMonth, $selectedYear);
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
                                class="rounded-xl border border-gray-100 bg-gradient-to-br from-white to-green-50 p-5 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-green-900/20">
                                <div class="flex flex-col items-center text-center">
                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40">
                                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                class="rounded-xl border border-gray-100 bg-gradient-to-br from-white to-amber-50 p-5 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:to-amber-900/20 relative">
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
                                    <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Selesai</p>
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
                                <div class="h-full rounded-full bg-gradient-to-r from-green-500 via-green-400 to-emerald-600 transition-all duration-700"
                                    style="width: {{ $stats['persentase_selesai'] }}%"></div>
                            </div>
                            <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $stats['penugasan_berjalan'] }} Berjalan</span>
                                <span>{{ $stats['penugasan_selesai'] }} Selesai</span>
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
                            :bestEmployee="app(\App\Services\DashboardAnalyticsService::class)->rankPegawai(1, request('month', now()->month), request('year', now()->year))->first()" />
                    </div>

                    {{-- ===== RANK EMPLOYEE ===== --}}
                    <div>
                        @auth
                            <x-profile.vis-rank-pegawai
                                :rankPegawai="app(\App\Services\DashboardAnalyticsService::class)->rankPegawai(5, request('month', now()->month), request('year', now()->year))"
                                :perPage="5" />
                        @endauth
                    </div>

                </div>

            </div>
        </div>

    @endif

</div>

@endsection
