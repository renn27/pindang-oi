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
            this.goToSelectedPeriod()
        },

        nextMonth(){
            if(this.selectedMonth === 12){
                this.selectedMonth = 1
                this.selectedYear++
            }else{
                this.selectedMonth++
            }
            this.goToSelectedPeriod()
        },

        goToSelectedPeriod(){
            const url = new URL(window.location.href)
            url.searchParams.set('month', this.selectedMonth)
            url.searchParams.set('year', this.selectedYear)
            window.location.href = `${url.pathname}?${url.searchParams.toString()}${url.hash}`
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

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-6">
    {{-- ===== FILTER BULAN GLOBAL ===== --}}
    <div>
        <div>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <svg class="h-4 w-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 uppercase tracking-wider">Filter Periode Global</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Memfilter semua statistik &amp; data di bawah ini</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        @click="prevMonth()"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-brand-200 bg-white text-brand-600 hover:bg-brand-50 dark:border-brand-700/50 dark:bg-gray-800 dark:text-brand-400 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <div class="min-w-[140px] text-center">
                        <span class="text-base font-bold text-gray-800 dark:text-white" x-text="months[selectedMonth - 1] + ' ' + selectedYear"></span>
                        <p x-show="isCurrentMonth()" class="text-xs text-green-600 dark:text-green-400 font-medium mt-0.5">● Bulan Ini</p>
                    </div>

                    <button
                        @click="nextMonth()"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-brand-200 bg-white text-brand-600 hover:bg-brand-50 dark:border-brand-700/50 dark:bg-gray-800 dark:text-brand-400 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    @if (!auth()->user()->isSuperUser())
        @php
            $currentUser = Auth::user();
            
            // Choose source collection depending on user
            $isSpecialUser = ($currentUser->nip_bps === '340017814');
            $rankSource = $isSpecialUser ? $rankPegawaiAll : $rankPegawaiAllTable;
            $rekapSource = $isSpecialUser ? $rekapAnggota : $rekapAnggotaTable;

            // Get user's task recap
            $myRekap = $rekapSource ? $rekapSource->firstWhere('id_pegawai', $currentUser->id_pegawai) : null;
            
            // Get user's performance rank & scores
            $myRankIndex = $rankSource ? $rankSource->search(fn($item) => $item->id_pegawai == $currentUser->id_pegawai) : false;
            $myRank = $myRankIndex !== false ? $myRankIndex + 1 : null;
            $myRankData = $myRankIndex !== false ? $rankSource[$myRankIndex] : null;
            $totalPegawaiRanked = $rankSource ? $rankSource->count() : 0;
        @endphp

        {{-- ===== RANGKUMAN KINERJA PEGAWAI ===== --}}
        <div class="mb-8 grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Left Side: Rekap Penugasan Anda -->
            <div class="lg:col-span-7 h-full rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex flex-col">
                <div class="flex flex-1 flex-col">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-blue-600 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Rekap Penugasan Anda</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Statistik penugasan & target Anda periode ini</p>
                        </div>
                    </div>

                    <!-- 6 Mini Stats Grid -->
                    <div class="grid flex-1 grid-cols-2 gap-3 sm:grid-cols-3 sm:auto-rows-fr">
                        <!-- Jml Penugasan -->
                        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-[0.06em]">Jml. Penugasan</p>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 9h6M9 13h4M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                                </span>
                            </div>
                            <div class="mt-8">
                                <p class="text-2xl font-extrabold leading-none tracking-tight text-gray-800 dark:text-gray-100">{{ $myRekap->total_penugasan ?? 0 }}</p>
                            </div>
                        </div>
                        <!-- Total Target -->
                        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-[0.06em]">Total Target</p>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/25 dark:text-purple-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m9-9H3"/></svg>
                                </span>
                            </div>
                            <div class="mt-8">
                                <p class="text-2xl font-extrabold leading-none tracking-tight text-gray-800 dark:text-gray-100">{{ $myRekap->total_target ?? 0 }}</p>
                            </div>
                        </div>
                        <!-- Dikirim -->
                        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-[0.06em]">Dikirim</p>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/25 dark:text-blue-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                </span>
                            </div>
                            <div class="mt-8">
                                <p class="text-2xl font-extrabold leading-none tracking-tight text-gray-800 dark:text-gray-100">{{ $myRekap->total_dikirim ?? 0 }}</p>
                            </div>
                        </div>
                        <!-- Diperiksa -->
                        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-[0.06em]">Diperiksa</p>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/25 dark:text-amber-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>
                            <div class="mt-8">
                                <p class="text-2xl font-extrabold leading-none tracking-tight text-gray-800 dark:text-gray-100">{{ $myRekap->total_diperiksa ?? 0 }}</p>
                            </div>
                        </div>
                        <!-- Revisi -->
                        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-[0.06em]">Revisi</p>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 dark:bg-red-900/25 dark:text-red-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 4.3L2.5 18a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 4.3a2 2 0 00-3.4 0z"/></svg>
                                </span>
                            </div>
                            <div class="mt-8">
                                <p class="text-2xl font-extrabold leading-none tracking-tight {{ ($myRekap->total_revisi ?? 0) > 0 ? 'text-red-700 dark:text-red-300' : 'text-gray-800 dark:text-gray-100' }}">{{ $myRekap->total_revisi ?? 0 }}</p>
                            </div>
                        </div>
                        <!-- Diterima -->
                        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-[0.06em]">Diterima</p>
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-900/25 dark:text-green-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                            <div class="mt-8">
                                <p class="text-2xl font-extrabold leading-none tracking-tight text-gray-800 dark:text-gray-100">{{ $myRekap->total_diterima ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Peringkat & Penilaian Kinerja Anda -->
            <div class="lg:col-span-5 h-full rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-100 bg-blue-50 text-blue-600 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m12 3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Peringkat & Kinerja</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Evaluasi performa Anda bulan ini</p>
                            </div>
                        </div>
                        
                        <!-- Rank Badge -->
                        @if ($myRank)
                            @php
                                $rankColors = [
                                    1 => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800/50 dark:bg-amber-900/20 dark:text-amber-300',
                                    2 => 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300',
                                    3 => 'border-orange-200 bg-orange-50 text-orange-700 dark:border-orange-800/50 dark:bg-orange-900/20 dark:text-orange-300',
                                ];
                                $badgeStyle = $rankColors[$myRank] ?? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800/50 dark:bg-blue-900/20 dark:text-blue-300';
                            @endphp
                            <div class="flex min-h-10 flex-col items-end justify-center gap-1">
                                <div class="inline-flex h-8 items-center rounded-full border px-3.5 {{ $badgeStyle }} text-sm font-bold uppercase tracking-wide">
                                    Rank #{{ $myRank }}
                                </div>
                                <span class="pr-1 text-[11px] leading-none text-gray-500 dark:text-gray-400">dari {{ $totalPegawaiRanked }} pegawai</span>
                            </div>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold dark:bg-gray-800 dark:text-gray-400">Belum Ada Rank</span>
                        @endif
                    </div>

                    @if ($myRankData)
                        <!-- Nilai Rata-rata & Stars -->
                        <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-800/45 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Nilai Akhir (Rata-rata)</span>
                                @php $score = (float) ($myRankData->rata_rata ?? 0); @endphp
                                <p class="mt-0.5 text-2xl font-extrabold {{ $score <= 0 ? 'text-gray-700 dark:text-gray-200' : 'text-blue-600 dark:text-blue-400' }}">
                                    {{ number_format($score, 2) }}%
                                </p>
                            </div>
                            <div class="flex flex-col items-end">
                                <!-- Rating Stars -->
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 0; $i < ($myRankData->star_full ?? 0); $i++)
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.966z"/>
                                        </svg>
                                    @endfor
                                    @if (($myRankData->star_half ?? 0) === 1)
                                        <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient id="hs-my-rank">
                                                    <stop offset="50%" stop-color="currentColor"/>
                                                    <stop offset="50%" stop-color="transparent"/>
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#hs-my-rank)" stroke="currentColor" stroke-width="1" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.966z"/>
                                        </svg>
                                    @endif
                                    @for ($i = 0; $i < ($myRankData->star_empty ?? 5); $i++)
                                        <svg class="w-4 h-4 text-gray-300 fill-current dark:text-gray-600" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.966z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 mt-1">{{ number_format($myRankData->rating_kirim ?? 0, 1) }} / 5.0</span>
                            </div>
                        </div>

                        <!-- Detail Nilai Komponen -->
                        <div class="mb-4 grid grid-cols-2 gap-2">
                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-2.5 dark:border-gray-800 dark:bg-gray-900">
                                <span class="block text-[11px] font-medium text-gray-500 dark:text-gray-400">RR Kirim</span>
                                <span class="mt-1 block text-sm font-bold text-gray-800 dark:text-gray-200">{{ number_format($myRankData->rr_kirim ?? 0, 2) }}%</span>
                            </div>
                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-2.5 dark:border-gray-800 dark:bg-gray-900">
                                <span class="block text-[11px] font-medium text-gray-500 dark:text-gray-400">Rating %</span>
                                <span class="mt-1 block text-sm font-bold text-gray-800 dark:text-gray-200">{{ number_format($myRankData->rating_persen ?? 0, 2) }}%</span>
                            </div>
                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-2.5 dark:border-gray-800 dark:bg-gray-900">
                                <span class="block text-[11px] font-medium text-gray-500 dark:text-gray-400">Skor Cepat</span>
                                <span class="mt-1 block text-sm font-bold text-gray-800 dark:text-gray-200">{{ number_format($myRankData->avg_skor_cepat ?? 0, 2) }}%</span>
                            </div>
                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-2.5 dark:border-gray-800 dark:bg-gray-900">
                                <span class="block text-[11px] font-medium text-gray-500 dark:text-gray-400">Koef. Beban</span>
                                <span class="mt-1 block text-sm font-bold text-gray-800 dark:text-gray-200">{{ number_format($myRankData->koefisien_beban ?? 1.0, 4) }}</span>
                            </div>
                        </div>
                    @else
                        <!-- Empty State for Rank -->
                        <div class="my-auto py-8 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-sm font-semibold">Tidak Ada Penilaian</p>
                            <p class="text-xs text-gray-400 mt-1">Anda belum memiliki penilaian kinerja pada periode ini.</p>
                        </div>
                    @endif
                </div>

                @if ($myRankData && $myRankData->has_penugasan_aktif)
                    <!-- Button "Lihat Detail Rumus" -->
                    <button
                        @click="$dispatch('open-calc-modal', {
                            nama: '{{ $currentUser->nama_pegawai }}',
                            rr_kirim: Number('{{ $myRankData->rr_kirim ?? 0 }}'),
                            rating_persen: Number('{{ $myRankData->rating_persen ?? 0 }}'),
                            skor_cepat: Number('{{ $myRankData->avg_skor_cepat ?? 0 }}'),
                            rata_rata: Number('{{ $myRankData->rata_rata ?? 0 }}'),
                            details: {{ Js::from($myRankData->details ?? []) }},
                            breakdown: {{ Js::from($myRankData->breakdown_formula ?? null) }}
                        })"
                        class="w-full mt-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors cursor-pointer dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500 dark:focus:ring-offset-gray-900"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Detail Rumus Penilaian
                    </button>
                @else
                    <button
                        disabled
                        class="w-full mt-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-400 dark:text-gray-600 cursor-not-allowed border border-gray-200/50 dark:border-gray-700/50"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Detail Rumus Penilaian
                    </button>
                @endif
            </div>
        </div>
    @endif

    @if (!auth()->user()->isSuperUser())

        @if (auth()->user()->isAnggotaTim() ||
                (auth()->user()->isKetuaTim() && auth()->user()->kegiatanYangDipimpin()->exists()))
            @auth
                @if (auth()->user()->isAnggotaTim())
                    {{-- ===== DAFTAR PENUGASAN BELUM SELESAI SBG ANGGOTA ===== --}}
                    @php $revisiCount = isset($revisiAsAnggota) ? $revisiAsAnggota->count() : 0; @endphp
                    <div class="mb-8 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm" x-data="{ 
                        activeTabAnggota: (new URLSearchParams(window.location.search).has('anggota_terlewat_page') ? 'terlewat' : (new URLSearchParams(window.location.search).has('anggota_berjalan_page') ? 'berjalan' : 'revisi')),
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
                        <div class="flex items-center gap-3 mb-5">
                            <span class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </span>
                            <h3 class="text-lg font-bold text-orange-500 dark:text-orange-400 animate-pulse tracking-wide">TO DO LIST ANGGOTA</h3>
                            <span class="bg-orange-100 dark:bg-orange-800 text-orange-600 dark:text-orange-400 text-xs font-semibold px-3 py-1 rounded-full">Harus Diselesaikan!</span>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700 mb-4">
                            {{-- Tab Revisi: selalu tampil untuk Anggota Tim --}}
                            <button @click="activeTabAnggota = 'revisi'"
                                :class="{'border-orange-500 text-orange-600 dark:text-orange-400 dark:border-orange-500': activeTabAnggota === 'revisi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabAnggota !== 'revisi'}"
                                class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" :class="activeTabAnggota === 'revisi' ? 'text-orange-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                Revisi Ketua Tim
                                @if($revisiCount > 0)
                                    <span class="ml-2 bg-orange-100 text-orange-700 py-0.5 px-2 rounded-full text-xs dark:bg-orange-900 dark:text-orange-200 font-bold animate-pulse">{{ $revisiCount }}</span>
                                @else
                                    <span class="ml-2 bg-gray-100 text-gray-400 py-0.5 px-2 rounded-full text-xs dark:bg-gray-800 dark:text-gray-500 font-medium">0</span>
                                @endif
                            </button>
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
                            {{-- Panel Revisi: selalu ada, tampilkan empty state jika kosong --}}
                            <div id="tab-anggota-revisi" x-show="activeTabAnggota === 'revisi'" style="display: none;" x-cloak class="transition-opacity duration-200">
                                @if($revisiCount > 0)
                                    <div class="mb-3 flex items-center gap-2 px-1">
                                        <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                        <p class="text-sm text-orange-700 dark:text-orange-400 font-medium">Pengiriman berikut telah direvisi oleh Ketua Tim dan perlu dikirim ulang segera.</p>
                                    </div>
                                    <x-tables.dashboard-penugasan-anggota :penugasans="collect($revisiAsAnggota->values())" />
                                @else
                                    <div class="flex flex-col items-center justify-center py-10 gap-3">
                                        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tidak Ada Revisi</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-xs">Semua pengiriman Anda saat ini tidak sedang dalam status revisi dari Ketua Tim. Kerja bagus!</p>
                                    </div>
                                @endif
                            </div>

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
            @endauth

            @auth
                @if (auth()->user()->isKetuaTim())
                    {{-- ===== DAFTAR PENUGASAN BELUM SELESAI SBG KETUA ===== --}}
                    @php $revisiDlCount = isset($revisiDlAsKetua) ? $revisiDlAsKetua->count() : 0; @endphp
                    <div class="mb-8 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm" x-data="{ 
                        activeTabKetua: (new URLSearchParams(window.location.search).has('ketua_terlewat_page') ? 'terlewat' : (new URLSearchParams(window.location.search).has('ketua_berjalan_page') ? 'berjalan' : 'revisi-dl')),
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
                        <div class="flex items-center gap-3 mb-5">
                            <span class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <h3 class="text-lg font-bold text-orange-500 dark:text-orange-400 animate-pulse tracking-wide">TO DO LIST KETUA TIM</h3>
                            <span class="bg-orange-100 dark:bg-orange-800 text-orange-600 dark:text-orange-400 text-xs font-semibold px-3 py-1 rounded-full">Wajib Diperiksa!</span>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700 mb-4">
                            {{-- Tab Revisi DL: selalu tampil untuk Ketua Tim --}}
                            <button @click="activeTabKetua = 'revisi-dl'"
                                :class="{'border-red-500 text-red-600 dark:text-red-400 dark:border-red-500': activeTabKetua === 'revisi-dl', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabKetua !== 'revisi-dl'}"
                                class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" :class="activeTabKetua === 'revisi-dl' ? 'text-red-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Revisi Tanggal DL
                                @if($revisiDlCount > 0)
                                    <span class="ml-2 bg-red-100 text-red-700 py-0.5 px-2 rounded-full text-xs dark:bg-red-900 dark:text-red-200 font-bold animate-pulse">{{ $revisiDlCount }}</span>
                                @else
                                    <span class="ml-2 bg-gray-100 text-gray-400 py-0.5 px-2 rounded-full text-xs dark:bg-gray-800 dark:text-gray-500 font-medium">0</span>
                                @endif
                            </button>
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
                            {{-- Panel Revisi DL: selalu ada --}}
                            <div id="tab-ketua-revisi-dl" x-show="activeTabKetua === 'revisi-dl'" style="display: none;" x-cloak class="transition-opacity duration-200">
                                @if($revisiDlCount > 0)
                                    <div class="mb-3 flex items-center gap-2 px-1">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                        <p class="text-sm text-red-700 dark:text-red-400 font-medium">Penugasan DL / Translok berikut ditolak oleh Pimpinan. Segera edit dan ajukan ulang.</p>
                                    </div>
                                    <x-tables.dashboard-penugasan-ketua :penugasans="collect($revisiDlAsKetua->values())" :showDlStatus="true" />
                                @else
                                    <div class="flex flex-col items-center justify-center py-10 gap-3">
                                        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tidak Ada Revisi DL</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-xs">Semua pengajuan Dinas Luar / Translok anggota tim Anda saat ini tidak ada yang ditolak oleh Pimpinan.</p>
                                    </div>
                                @endif
                            </div>

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

        @if ($bestEmployee)
            {{-- ===== BEST EMPLOYEE FOR REGULAR EMPLOYEES ===== --}}
            <div class="mt-8">
                <x-profile.employe-rank-card
                    :bestEmployee="$bestEmployee"
                    :showHeader="true" />
            </div>
        @endif

        {{-- ===== RANK EMPLOYEE FOR REGULAR EMPLOYEES ===== --}}
        <div id="container-rank-pegawai" class="mt-8">
            @auth
                <x-dashboard.vis-rank-pegawai
                    :rankPegawaiAll="$rankPegawaiAllTable"
                    :perPage="$rankPegawaiPerPage"
                    :perPageOptions="$rankPegawaiPerPageOptions" />
            @endauth
        </div>
    @else()
        {{-- ===== CARD BESAR ANALYTICS DASHBOARD ===== --}}
        <div class="mb-8">
            <div class="rounded-2xl border border-brand-200/60 bg-white p-6 dark:border-brand-800/30 dark:bg-gray-900">

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
                            <div class="flex items-center gap-1.5 rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-sm font-semibold text-brand-700 dark:border-brand-800/40 dark:bg-brand-900/20 dark:text-brand-300">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span x-text="months[selectedMonth - 1] + ' ' + selectedYear"></span>
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
                        <x-profile.employe-rank-card
                            :bestEmployee="$bestEmployee"
                            :showHeader="true" />
                    </div>

                    {{-- ===== RANK EMPLOYEE ===== --}}
                    <div id="container-rank-pegawai">
                        @auth
                            <x-dashboard.vis-rank-pegawai
                                :rankPegawaiAll="$rankPegawaiAllTable"
                                :perPage="$rankPegawaiPerPage"
                                :perPageOptions="$rankPegawaiPerPageOptions" />
                        @endauth
                    </div>

                </div>

            </div>
        </div>

    @endif

    @if(auth()->user()->isSuperUser())
        <div class="mb-8">
            <x-dashboard.vis-rekap-penugasan-pegawai
                :rekapAnggota="$rekapAnggotaTable"
                :selectedMonth="$selectedMonth"
                :selectedYear="$selectedYear"
                :perPage="$rekapPenugasanPerPage"
                :perPageOptions="$rekapPerPageOptions" />
        </div>

        <div class="mb-8">
            <x-dashboard.vis-rekap-sub-kegiatan
                :rekapSubKegiatan="$rekapSubKegiatanTable"
                :selectedMonth="$selectedMonth"
                :selectedYear="$selectedYear"
                :perPage="10"
                :perPageOptions="$rekapPerPageOptions" />
        </div>
    @endif

    {{-- Modal Perhitungan Rumus Component --}}
    <x-dashboard.modal-perhitungan-rumus />

</div>

@endsection
