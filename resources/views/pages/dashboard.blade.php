@extends('layouts.dashboard')

@section('content')
    {{-- ===== HEADER IDENTITAS ===== --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6">
        @auth
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-500">Selamat Datang</p>
                    <h1 class="text-xl font-bold text-gray-800">
                        {{ Auth::user()->nama_pegawai }}
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Role</span>
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-sm font-semibold text-brand-700">
                        {{ Auth::user()->active_role ?? 'Anggota Tim' }}
                    </span>
                </div>
            </div>
        @else
            <p class="text-gray-500">Belum login</p>
        @endauth
    </div>

    {{-- ===== SECTION BEST EMPLOYEE ===== --}}
    <div class="mb-8">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Best Employee</h2>
            <p class="text-sm text-gray-500">Karyawan dengan performa terbaik bulan ini</p>
        </div>

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            {{-- <div class="col-span-12 xl:col-span-6">
                <x-profile.employe-work-target />
            </div> --}}

            <div class="col-span-12 xl:col-span-6">
                <div class="mb-5 rounded-2xl space-6 border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-4">
                        <x-profile.employe-rank-card />
                    </div>
                </div>
                {{-- <x-profile.resume-kegiatan /> --}}
            </div>
        </div>
    </div>

    {{-- ===== SECTION PENUGASAN ASTRI ===== --}}
    <div class="mb-8">
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 dark:from-gray-800 dark:to-gray-900 dark:border-gray-700">
            <div class="mb-6 text-center">
                <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">Rekap Penugasan dari {{ Auth::user()->nama_pegawai }}</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Setiap tugas adalah kesempatan untuk tumbuh dan menunjukkan potensi terbaikmu.
                    Laksanakan dengan penuh tanggung jawab dan dedikasi!
                </p>
            </div>

            <div class="col-span-12 xl:col-span-5 space-y-6">
                @auth
                    @if (auth()->user()->isAnggota())
                        <x-profile.vis-total-penugasan-pegawai />
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- ===== SECTION ALL PENILAIAN KARYAWAN ===== --}}
    <div class="mb-8">
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-6">
                <h2 class="mb-2 text-xl font-bold text-gray-800 dark:text-white">All Penilaian Karyawan</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Pantau perkembangan dan capaian seluruh tim dalam satu dashboard yang terintegrasi.
                </p>
            </div>

            <div class="col-span-12 xl:col-span-5 space-y-6">
                @auth
                    <x-profile.vis-rank-pegawai />
                @endauth
            </div>
        </div>
    </div>

@endsection
