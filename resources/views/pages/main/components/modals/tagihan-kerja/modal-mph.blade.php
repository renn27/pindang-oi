<x-ui.smart-modal id="modal-mph" class="max-w-6xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-mph') return;
        open = true;
    ">
    
    <div class="relative flex h-[90vh] w-full max-w-[1200px] flex-col overflow-hidden rounded-3xl bg-white">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 bg-white px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50">
                            <svg class="h-5 w-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-gray-900">Matriks Peran Hasil (MPH)</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $title }} - Tahun 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BODY (SCROLL DI SINI) -->
        <div class="flex-1 overflow-y-auto bg-gray-50/50 px-6 py-6">
            @if($kegiatans->isEmpty())
                {{-- EMPTY STATE --}}
                <div class="flex h-full items-center justify-center">
                    <div class="w-full max-w-xl">
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
                            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50">
                                <svg class="h-10 w-10 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M9 17v-2a4 4 0 014-4h4M9 5h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                                </svg>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-900">
                                Belum Ada Matriks Peran Hasil (MPH)
                            </h3>

                            <p class="mt-2 max-w-md text-sm text-gray-500">
                                Data kegiatan dan penugasan pegawai belum tersedia.
                                Silakan tambahkan kegiatan terlebih dahulu untuk menyusun MPH.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Statistik Ringkas -->
                <div class="mb-6 grid grid-cols-4 gap-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Kegiatan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $kegiatans->count() }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Sub Kegiatan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $kegiatans->flatMap->subKegiatans->count() }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7M13 18h7M9 6v12" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Penugasan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ 
                                    $kegiatans
                                        ->flatMap->subKegiatans
                                        ->flatMap->penugasans
                                        ->count()
                                }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 11l3 3L22 4M2 12h7M2 18h7M2 6h7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Pegawai</p>
                                <p class="text-2xl font-bold text-gray-900">{{
                                    $kegiatans
                                        ->flatMap->subKegiatans
                                        ->flatMap->penugasans
                                        ->pluck('anggota.id_pegawai')
                                        ->unique()
                                        ->count()
                                }}</p>
                            </div>
                            <div class="rounded-lg bg-green-50 p-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0h-6m3 0h-6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Kegiatan dan Sub-Kegiatan -->
                <div class="space-y-6">
                    <!-- Kegiatan 1 -->
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <!-- Header Kegiatan -->
                        <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-sm font-bold text-white">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $kegiatan?->nama_rk_kegiatan }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">Ketua: {{ $kegiatan?->penanggungJawab->nama_pegawai    }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- <!-- Daftar Sub Kegiatan -->
                        <div class="divide-y divide-gray-100">
                            <!-- Sub Kegiatan 1.1 -->
                            @foreach ($kegiatan->subKegiatans as $subKegiatan)
                                <div class="px-6 py-5">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800">
                                                {{ $loop->iteration }}
                                            </div>
                                            <h4 class="font-semibold text-gray-900">{{ $subKegiatan->nama_sub_kegiatan }}</h4>
                                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                                {{ $subKegiatan->penugasans->count() }} Pegawai
                                            </span>
                                        </div>
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                            Berjalan
                                        </span>
                                    </div>
                                    
                                    <!-- Daftar Anggota Sub Kegiatan -->
                                    <div class="space-y-3">
                                        @foreach($subKegiatan->penugasans as $penugasan)
                                            <div class="group flex items-center justify-between rounded-xl border border-gray-100 bg-white p-4 hover:border-brand-200 hover:bg-brand-50/50 transition-colors">
                                                <div class="flex items-center gap-4">
                                                    <div class="relative">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                                                            <span class="text-sm font-semibold text-blue-700">P{{ $loop->iteration }}</span>
                                                        </div>
                                                        @if($loop->iteration == 1)
                                                            <div class="absolute -right-1 -top-1 h-4 w-4 rounded-full border-2 border-white bg-amber-500"></div>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-[200px]">
                                                        <div class="font-medium text-gray-900">{{ $penugasan->anggota->nama_pegawai }}</div>
                                                        <div class="text-sm text-gray-500">Jabatan : {{ $penugasan->anggota->jabatan }}</div>
                                                    </div>
                                                    <div class="hidden md:block">
                                                        <div class="text-sm text-gray-600">
                                                            <span class="font-medium">Jenis Kegiatan:</span>
                                                            <span class="ml-2 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs">{{ $penugasan->jenisKegiatan->jenis_kegiatan }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-6">
                                                    <!-- TARGET KEGIATAN (BARU) -->
                                                    <div class="text-right">
                                                        <div class="text-xs text-gray-500">Target Kegiatan</div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $penugasan->target }} {{ $penugasan->satuan_target }}</div>
                                                    </div>
                                                    
                                                    <!-- TARGET WAKTU -->
                                                    <div class="text-right">
                                                        <div class="text-xs text-gray-500">Target Waktu</div>
                                                        <div class="text-sm font-medium text-gray-900">{{
                                                            ($penugasan->tanggal_mulai && $penugasan->tanggal_selesai)
                                                                ? (
                                                                    $penugasan->tanggal_mulai->equalTo($penugasan->tanggal_selesai)
                                                                        ? $penugasan->tanggal_mulai->translatedFormat('D, d M Y')
                                                                        : $penugasan->tanggal_mulai->translatedFormat('D, d M Y') . ' - ' . $penugasan->tanggal_selesai->translatedFormat('D, d M Y')
                                                                )
                                                                : '-'
                                                        }}
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div> --}}

                        <!-- Daftar Sub Kegiatan -->
                        <div class="divide-y divide-gray-100">
                            @foreach ($kegiatan->subKegiatans as $subKegiatan)
                                <div class="px-6 py-5">
                                    
                                    <!-- HEADER SUB KEGIATAN -->
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800">
                                                {{ $loop->iteration }}
                                            </div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $subKegiatan->nama_sub_kegiatan }}
                                            </h4>
                                            <span
                                                class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                                {{ $subKegiatan->penugasans->count() }} Pegawai
                                            </span>
                                        </div>
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                            Berjalan
                                        </span>
                                    </div>

                                    <!-- DAFTAR ANGGOTA -->
                                    <div class="space-y-3">
                                        @foreach($subKegiatan->penugasans as $penugasan)
                                            <div
                                                class="group rounded-xl border border-gray-100 bg-white p-4 transition-colors hover:border-brand-200 hover:bg-brand-50/50">

                                                <!-- GRID TABLE-LIKE -->
                                                <div
                                                    class="grid grid-cols-1 items-center gap-y-4 gap-x-6 md:grid-cols-[auto_220px_180px_160px_220px]">

                                                    <!-- PROFIL -->
                                                    <div class="flex items-center gap-4">
                                                        <div class="relative">
                                                            @if($penugasan->anggota->photo)
                                                                <img
                                                                    src="{{ asset('storage/' . $penugasan->anggota->photo) }}"
                                                                    alt="{{ $penugasan->anggota->nama_pegawai }}"
                                                                    class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                                                >
                                                            @else
                                                                <div
                                                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-blue-50">
                                                                    <span class="text-sm font-semibold text-blue-700">
                                                                        P{{ $loop->iteration }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>


                                                    <!-- NAMA & JABATAN -->
                                                    <div>
                                                        <div class="font-medium text-gray-900">
                                                            {{ $penugasan->anggota->nama_pegawai }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            Jabatan : {{ $penugasan->anggota->jabatan }}
                                                        </div>
                                                    </div>

                                                    <!-- JENIS KEGIATAN -->
                                                    <div class="hidden md:block">
                                                        <div class="text-sm text-gray-600">
                                                            <span class="font-medium">Jenis Kegiatan</span>
                                                        </div>
                                                        <span
                                                            class="mt-1 inline-block rounded-full bg-gray-100 px-2.5 py-0.5 text-xs">
                                                            {{ $penugasan->jenisKegiatan->jenis_kegiatan }}
                                                        </span>
                                                    </div>

                                                    <!-- TARGET KEGIATAN -->
                                                    <div class="text-right md:text-left">
                                                        <div class="text-xs text-gray-500">Target Kegiatan</div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $penugasan->target }} {{ $penugasan->satuan_target }}
                                                        </div>
                                                    </div>

                                                    <!-- TARGET WAKTU -->
                                                    <div class="text-right md:text-left">
                                                        <div class="text-xs text-gray-500">Target Waktu</div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{
                                                                ($penugasan->tanggal_mulai && $penugasan->tanggal_selesai)
                                                                    ? (
                                                                        $penugasan->tanggal_mulai->equalTo($penugasan->tanggal_selesai)
                                                                            ? $penugasan->tanggal_mulai->translatedFormat('D, d M Y')
                                                                            : $penugasan->tanggal_mulai->translatedFormat('D, d M Y') . ' - ' . $penugasan->tanggal_selesai->translatedFormat('D, d M Y')
                                                                    )
                                                                    : '-'
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 bg-white px-6 py-5">
            <div class="flex justify-end gap-3">

                @unless($kegiatans->isEmpty())
                    <button type="button"
                        class="flex items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-medium text-green-700 hover:bg-green-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </button>
                @endunless

                <button @click="open = false" type="button"
                    class="flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>

            </div>
        </div>


    </div>
</x-ui.smart-modal>