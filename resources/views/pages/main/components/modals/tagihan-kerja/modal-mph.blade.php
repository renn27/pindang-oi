<x-ui.smart-modal id="modal-mph" class="max-w-6xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-mph') return;
        open = true;
    ">

    <div class="relative flex h-[90vh] w-full max-w-[1200px] flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 bg-white px-6 py-5 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-900/30">
                            <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">Matriks Peran Hasil (MPH)</h4>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $title }} - Tahun <span id="mphTahunLabel">2026</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BODY (SCROLL DI SINI) -->
        <div class="flex-1 overflow-y-auto bg-gray-50/50 px-6 py-6 dark:bg-gray-900">
            @if ($kegiatans->isEmpty())
                {{-- EMPTY STATE --}}
                <div class="flex h-full items-center justify-center">
                    <div class="w-full max-w-xl">
                        <div
                            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/30">
                                <svg class="h-10 w-10 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M9 17v-2a4 4 0 014-4h4M9 5h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Belum Ada Matriks Peran Hasil (MPH)
                            </h3>

                            <p class="mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">
                                Data kegiatan dan penugasan pegawai belum tersedia.
                                Silakan tambahkan kegiatan terlebih dahulu untuk menyusun MPH.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Statistik Ringkas -->
                <div class="mb-6 grid grid-cols-4 gap-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Kegiatan</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kegiatans->count() }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2 dark:bg-blue-900/30">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Sub Kegiatan</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $kegiatans->flatMap->subKegiatans->count() }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2 dark:bg-blue-900/30">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7M13 18h7M9 6v12" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Penugasan</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $kegiatans->flatMap->subKegiatans->flatMap->penugasans->count() }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2 dark:bg-blue-900/30">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 11l3 3L22 4M2 12h7M2 18h7M2 6h7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pegawai</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $kegiatans->flatMap->subKegiatans->flatMap->penugasans->pluck('anggota.id_pegawai')->unique()->count() }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-green-50 p-2 dark:bg-green-900/30">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0h-6m3 0h-6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Kegiatan dan Sub-Kegiatan -->
                <div class="space-y-6">
                    <!-- Kegiatan 1 -->
                    @foreach ($kegiatans as $kegiatan)
                        <div data-tahun="{{ $kegiatan?->tahun_kegiatan }}" class="mph-kegiatan-card overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <!-- Header Kegiatan -->
                            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white px-6 py-5 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500 text-sm font-bold text-white dark:bg-brand-600">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $kegiatan?->nama_rk_kegiatan }}</h3>
                                            <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Ketua:
                                                {{ $kegiatan?->penanggungJawab->nama_pegawai }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Sub Kegiatan -->
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($kegiatan->subKegiatans as $subKegiatan)
                                    <div class="px-6 py-5">

                                        <!-- HEADER SUB KEGIATAN -->
                                        <div class="mb-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $subKegiatan->nama_sub_kegiatan }}
                                                </h4>
                                                <span
                                                    class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                    {{ $subKegiatan->penugasans->count() }} Pegawai
                                                </span>
                                            </div>
                                            <span
                                                class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                Berjalan
                                            </span>
                                        </div>

                                        <!-- DAFTAR ANGGOTA -->
                                        <div class="space-y-3">
                                            @foreach ($subKegiatan->penugasans as $penugasan)
                                                <div
                                                    class="group rounded-xl border border-gray-100 bg-white p-4 transition-colors hover:border-brand-200 hover:bg-brand-50/50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-brand-700 dark:hover:bg-brand-900/20">

                                                    <!-- GRID TABLE-LIKE -->
                                                    <div
                                                        class="grid grid-cols-1 items-center gap-y-4 gap-x-6 md:grid-cols-[auto_220px_180px_160px_220px]">

                                                        <!-- PROFIL -->
                                                        <div class="flex items-center gap-4">
                                                            <div class="relative">
                                                                @if ($penugasan->anggota->photo)
                                                                    <img src="{{ asset('storage/' . $penugasan->anggota->photo) }}"
                                                                        alt="{{ $penugasan->anggota->nama_pegawai }}"
                                                                        class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                                                                @else
                                                                    <div
                                                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-900/10">
                                                                        <span class="text-sm font-semibold text-blue-700 dark:text-blue-400">
                                                                            P{{ $loop->iteration }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>


                                                        <!-- NAMA & JABATAN -->
                                                        <div>
                                                            <div class="font-medium text-gray-900 dark:text-white">
                                                                {{ $penugasan->anggota->nama_pegawai }}
                                                            </div>
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                Jabatan : {{ $penugasan->anggota->jabatan }}
                                                            </div>
                                                        </div>

                                                        <!-- JENIS KEGIATAN -->
                                                        <div class="hidden md:block">
                                                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                                                <span class="font-medium">Jenis Kegiatan</span>
                                                            </div>
                                                            <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{$penugasan->jenisKegiatan ? 'bg-teal-100 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400' : 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' }}">
                                                                {{ $penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri' }}
                                                            </span>
                                                        </div>

                                                        <!-- TARGET KEGIATAN -->
                                                        <div class="text-right md:text-left">
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">Target Kegiatan</div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $penugasan->target }} {{ $penugasan->satuan_target }}
                                                            </div>
                                                        </div>

                                                        <!-- TARGET WAKTU -->
                                                        <div class="text-right md:text-left">
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">Target Waktu</div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $penugasan->tanggal_mulai && $penugasan->tanggal_selesai
                                                                    ? ($penugasan->tanggal_mulai->equalTo($penugasan->tanggal_selesai)
                                                                        ? $penugasan->tanggal_mulai->translatedFormat('D, d M Y')
                                                                        : $penugasan->tanggal_mulai->translatedFormat('D, d M Y') .
                                                                            ' - ' .
                                                                            $penugasan->tanggal_selesai->translatedFormat('D, d M Y'))
                                                                    : '-' }}
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
                    @endforeach
                    <div id="noMphKegiatanRow" class="hidden rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/30 mx-auto">
                            <svg class="h-10 w-10 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 17v-2a4 4 0 014-4h4M9 5h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Tidak Ada Matriks Peran Hasil (MPH)
                        </h3>
                        <p class="mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400 mx-auto">
                            Tidak ada data kegiatan dan penugasan pegawai untuk tahun yang dipilih.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 bg-white px-6 py-5 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex justify-end gap-3">

                @unless ($kegiatans->isEmpty())
                    <a href="{{ route('kegiatan.export-mph', $bidang->slug) }}"
                        class="flex items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-medium text-green-700 hover:bg-green-100 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                @endunless

                <button @click="open = false" type="button"
                    class="flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Tutup
                </button>

            </div>
        </div>


    </div>
    <script>
        function filterMphByTahun(selectedYear) {
            const label = document.getElementById('mphTahunLabel');
            if (label) label.innerText = selectedYear;

            const cards = document.querySelectorAll('.mph-kegiatan-card');
            const emptyRow = document.getElementById('noMphKegiatanRow');
            let visibleCount = 0;

            cards.forEach(card => {
                if (card.getAttribute('data-tahun') === selectedYear) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (visibleCount === 0) {
                if (emptyRow) emptyRow.classList.remove('hidden');
            } else {
                if (emptyRow) emptyRow.classList.add('hidden');
            }
        }
    </script>
</x-ui.smart-modal>
