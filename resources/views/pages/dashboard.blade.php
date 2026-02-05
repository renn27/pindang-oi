@extends('layouts.dashboard')

@section('content')
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
                        {{ Auth::user()->active_role ?? 'Anggota Tim' }}
                    </span>
                </div>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-300">Belum login</p>
        @endauth
    </div>

    @if (!auth()->user()->isAnggota())
        {{-- ===== SECTION STATISTIK KEGIATAN ===== --}}
        <div class="mb-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-gray-900">
                @php
                    $stats = app(\App\Services\DashboardAnalyticsService::class)->getDashboardStats();
                @endphp

                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Statistik Kegiatan</h3>
                    <div
                        class="rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ now()->format('F Y') }}
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
        </div>
    @endif

    {{-- ===== SECTION PENUGASAN ASTRI ===== --}}
    @if (auth()->user()->isAnggota())
        <div class="mb-8">
            <div
                class="rounded-2xl border border-gray-200 p-6 dark:from-gray-800 dark:to-gray-900 bg-white dark:bg-gray-900 dark:border-gray-800">
                <div class="mb-6 text-center">
                    <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">Rekap Penugasan dari
                        {{ Auth::user()->nama_pegawai }}</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        Setiap tugas adalah kesempatan untuk tumbuh dan menunjukkan potensi terbaikmu.
                        Laksanakan dengan penuh tanggung jawab dan dedikasi!
                    </p>
                </div>

                <div class="col-span-12 xl:col-span-5 space-y-6">
                    @auth
                        <x-profile.vis-total-penugasan-pegawai :totalpenugasanPegawai="app(\App\Services\DashboardAnalyticsService::class)->summaryPenugasanAnggota(
                            Auth::user()->id_pegawai,
                        )" />
                    @endauth
                </div>
            </div>
        </div>
    @endif

    {{-- ===== SECTION BEST EMPLOYEE ===== --}}
    <div class="mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Best Employee</h3>
                <div
                    class="rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                    {{ now()->format('F Y') }}
                </div>
            </div>
            <div class="w-full">
                <x-profile.employe-rank-card :bestEmployee="app(\App\Services\DashboardAnalyticsService::class)->rankPegawai(1)->first()" />
            </div>
        </div>
    </div>



    {{-- ===== SECTION ALL PENILAIAN KARYAWAN ===== --}}
    <div class="w-full">
        @auth
            <x-profile.vis-rank-pegawai :rankPegawai="app(\App\Services\DashboardAnalyticsService::class)->rankPegawai()" :perPage="5" />
        @endauth
    </div>
@endsection
