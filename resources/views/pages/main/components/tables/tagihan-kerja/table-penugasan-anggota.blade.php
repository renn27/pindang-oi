<div class="border border-gray-200 rounded-lg overflow-hidden">
    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Daftar Penugasan</h3>
            <p class="text-sm text-gray-500">Sub Kegiatan: {{ $subKegiatan->nama_sub_kegiatan }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Toggle untuk Pengiriman -->
            <button id="toggle-pengiriman" 
                    class="toggle-section-btn flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    data-section="pengiriman">
                <svg class="w-4 h-4 mr-2 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Pengiriman</span>
            </button>
            
            <!-- Toggle untuk Penerimaan -->
            <button id="toggle-penerimaan" 
                    class="toggle-section-btn flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    data-section="penerimaan">
                <svg class="w-4 h-4 mr-2 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Penerimaan</span>
            </button>
            
            <!-- Toggle Semua -->
            <button id="toggle-semua"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg hover:bg-blue-700">
                Tampilkan Semua
            </button>
        </div>
    </div>

    <div class="overflow-x-auto relative">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50 z-10">
                        No.
                    </th>
                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky left-[60px] bg-gray-50 z-10">
                        Nama
                    </th>
                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Jenis Kegiatan
                    </th>
                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Target
                    </th>
                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Waktu Pelaksanaan
                    </th>
                    
                    <!-- Kolom PENGIRIMAN (bisa di-toggle) -->
                    <th colspan="3" 
                        class="section-pengiriman px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border-l-2 border-blue-200"
                        data-section="pengiriman">
                        Pengiriman
                    </th>
                    
                    <!-- Kolom PENERIMAAN (bisa di-toggle) -->
                    <th colspan="4" 
                        class="section-penerimaan px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border-l-2 border-green-200"
                        data-section="penerimaan">
                        Penerimaan
                    </th>
                    
                    <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase sticky right-0 bg-gray-50 z-10">
                        Aksi
                    </th>
                </tr>
                <tr class="bg-gray-50">
                    <!-- Sub-kolom PENGIRIMAN -->
                    <th class="section-pengiriman px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-l-2 border-blue-200"
                        data-section="pengiriman">
                        Detail
                    </th>
                    <th class="section-pengiriman px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                        data-section="pengiriman">
                        RR (%)
                    </th>
                    <th class="section-pengiriman px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                        data-section="pengiriman">
                        Ketepatan Waktu
                    </th>
                    
                    <!-- Sub-kolom PENERIMAAN -->
                    <th class="section-penerimaan px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-l-2 border-green-200"
                        data-section="penerimaan">
                        Detail
                    </th>
                    <th class="section-penerimaan px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                        data-section="penerimaan">
                        RR (%)
                    </th>
                    <th class="section-penerimaan px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                        data-section="penerimaan">
                        Ketepatan Waktu
                    </th>
                    <th class="section-penerimaan px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                        data-section="penerimaan">
                        Bukti Dukung
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($subKegiatan->penugasans as $index => $penugasan)
                    <tr class="hover:bg-gray-50"
                        data-target="{{ $penugasan->target }}"
                        data-deadline="{{ $penugasan->tanggal_selesai }}"
                        data-kirim-jumlah="{{ $penugasan->latestPengiriman?->jumlah_dikirim ?? 0 }}"
                        data-kirim-tanggal="{{ $penugasan->latestPengiriman?->tanggal_pengiriman ?? '' }}"
                        data-terima-jumlah="{{ $penugasan->latestPenerimaan?->jumlah_diterima ?? 0 }}"
                        data-terima-tanggal="{{ $penugasan->latestPenerimaan?->tanggal_penerimaan ?? '' }}">
                        
                        <!-- Kolom tetap (tidak bisa di-toggle) -->
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 text-center sticky left-0 bg-white z-10">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 sticky left-[60px] bg-white z-10">
                            {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center">
                            {{ $penugasan->target ?? '-' }} 
                            <span class="block text-xs text-orange-800">{{ $penugasan->satuan_target ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700 text-center">
                            {{
                                    ($penugasan->tanggal_mulai && $penugasan->tanggal_selesai)
                                        ? (
                                            $penugasan->tanggal_mulai->equalTo($penugasan->tanggal_selesai)
                                                ? $penugasan->tanggal_mulai->translatedFormat('D, d M Y')
                                                : $penugasan->tanggal_mulai->translatedFormat('D, d M Y') . ' - ' . $penugasan->tanggal_selesai->translatedFormat('D, d M Y')
                                        )
                                        : '-'
                                }}
                        </td>
                        
                        <!-- Kolom PENGIRIMAN (bisa di-toggle) -->
                        <td class="section-pengiriman px-4 py-3 text-sm text-gray-700 border-l-2 border-blue-50">
                            <div>
                                <p class="text-xs text-gray-500 pl-4">
                                    {{ $penugasan->latestPengiriman?->tanggal_pengiriman?->translatedFormat('D, d M Y') ?? 'belum dikirim' }}
                                </p>
                                <p class="text-xs text-gray-500 pl-4">Jumlah :
                                    {{ $penugasan->latestPengiriman->jumlah_dikirim ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 pl-4">Dikirim melalui
                                    {{ $penugasan->latestPengiriman->media_pengiriman ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="section-pengiriman px-4 py-3 text-sm text-gray-700 text-center">
                            {{ $penugasan->rr_kirim ? $penugasan->rr_kirim . '%' : '-' }}
                        </td>
                        <td class="section-pengiriman px-4 py-3 text-sm text-gray-700 text-center">
                            <div class="flex justify-center">
                                <div class="flex justify-center rating-kirim"></div>
                            </div>
                        </td>
                        
                        <!-- Kolom PENERIMAAN (bisa di-toggle) -->
                        <td class="section-penerimaan px-4 py-3 text-sm text-gray-700 border-l-2 border-green-50">
                            <div>
                                <p class="text-xs text-gray-500 pl-4">
                                    {{ $penugasan->latestPenerimaan?->tanggal_penerimaan?->translatedFormat('D, d M Y') ?? 'belum diterima' }}
                                </p>
                                <p class="text-xs text-gray-500 pl-4">
                                    Jumlah: {{ $penugasan->latestPenerimaan?->jumlah_diterima ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 pl-4">
                                    Diterima melalui:
                                    {{ $penugasan->latestPengiriman?->media_pengiriman ?? '-' }}
                                </p>
                            </div>
                        </td>
                        <td class="section-penerimaan px-4 py-3 text-sm text-gray-700 text-center">
                            {{ $penugasan->rr_terima ? $penugasan->rr_terima . '%' : '-' }}
                        </td>
                        <td class="section-penerimaan px-4 py-3 text-sm text-gray-700 text-center">
                            <div class="flex justify-center">
                                <div class="flex justify-center rating-terima"></div>
                            </div>
                        </td>
                        <td class="section-penerimaan px-4 py-3 text-sm text-gray-700 text-center">
                            <a href="{{ $penugasan->latestPengiriman?->bukti_dukung ?: 'https://www.youtube.com/' }}"
                                target="_blank" rel="noopener noreferrer"
                                title="{{ $penugasan->bukti_dukung ? 'Buka bukti dukung' : 'Belum ada bukti dukung' }}"
                                class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800">
                                {{ $penugasan->latestPengiriman?->bukti_dukung ? 'Lihat Bukti' : 'Belum Ada' }}
                            </a>
                        </td>
                        
                        <td class="px-4 py-3 text-sm text-gray-700 text-center border-r border-gray-200">
                            <!-- Container untuk tombol saja -->
                            <div class="relative inline-block" x-data="{
                                showDropdown: false,
                                dropdownPosition: { x: 0, y: 0 },
                                calculatePosition(button) {
                                    const rect = button.getBoundingClientRect();
                                    const dropdownWidth = 192;
                                    // Tinggi untuk 5 tombol: (5 * 44px) ≈ 220px, plus padding
                                    const dropdownHeight = 236;

                                    // Start dengan posisi di bawah tombol, rata kiri dengan tombol
                                    let left = rect.left;
                                    let top = rect.bottom + 8;

                                    // Cek jika dropdown akan keluar dari viewport KANAN
                                    // Gunakan window.innerWidth - 20 (margin) bukan -50
                                    if (left + dropdownWidth > window.innerWidth - 20) {
                                        // Posisikan di KIRI tombol
                                        left = rect.left - dropdownWidth;

                                        // Jika masih di luar kiri viewport
                                        if (left < 10) {
                                            // Posisikan di dalam viewport dengan sedikit margin
                                            left = 10;
                                        }
                                    }

                                    // Cek jika dropdown akan keluar dari viewport KIRI
                                    if (left < 10) {
                                        left = 10;
                                    }

                                    // Cek jika dropdown akan keluar dari viewport BAWAH
                                    if (top + dropdownHeight > window.innerHeight - 20) {
                                        // Pindah ke ATAS tombol
                                        top = rect.top - dropdownHeight - 8;

                                        // Jika juga tidak muat di atas
                                        if (top < 10) {
                                            top = 10;
                                        }
                                    }

                                    return { x: left, y: top };
                                },
                                openDropdown(event) {
                                    const button = event.currentTarget;
                                    this.dropdownPosition = this.calculatePosition(button);
                                    this.showDropdown = true;
                                },
                                closeDropdown() {
                                    this.showDropdown = false;
                                }
                            }"
                                x-on:mouseleave="closeDropdown()">

                                <!-- Tombol utama dengan hover -->
                                <button x-on:mouseenter="openDropdown($event)"
                                    class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800">
                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18"
                                        fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                    </svg>
                                    Aksi
                                </button>

                                @php
                                    $latestPengiriman = $penugasan->latestPengiriman;
                                    $latestPenerimaan = $penugasan->latestPenerimaan;

                                    $statusPenerimaan = $latestPenerimaan?->status;

                                    // boleh melakukan aksi
                                    $bolehAksi =
                                        is_null($latestPengiriman) ||        // belum ada pengiriman
                                        $statusPenerimaan === 'Direvisi';    // sudah ada tapi direvisi
                                @endphp
                                <!-- Dropdown menu FIXED POSITION -->
                                <div x-show="showDropdown" x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed z-[9999] bg-white rounded-lg shadow-xl border border-gray-200 min-w-[192px]"
                                    :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                    x-on:mouseenter="showDropdown = true" x-on:mouseleave="closeDropdown()">

                                    @can('update', $penugasan)
                                        {{-- @if($bolehAksi) --}}
                                            {{-- Edit Data Penugasan --}}
                                            <button
                                                class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 whitespace-nowrap border-b border-gray-100"
                                                @click="
                                                    const payload = {
                                                        modalId: 'modal-penugasan-anggota',
                                                        mode: 'edit',
                                                        key: '{{ $penugasan->id_penugasan }}',
                                                        data: {
                                                            id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                            nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                            id_anggota: @js($penugasan->id_anggota),
                                                            nama_anggota: @js($penugasan->anggota?->nama_pegawai),
                                                            id_jenis_kegiatan: @js($penugasan->jenisKegiatan->id),
                                                            target: @js($penugasan->target),
                                                            satuan_target: @js($penugasan->satuan_target),
                                                            tanggal_mulai: @js(optional($penugasan->tanggal_mulai)->format('Y-m-d')),
                                                            tanggal_selesai: @js(optional($penugasan->tanggal_selesai)->format('Y-m-d')),

                                                            {{-- tanggal_mulai: @js($penugasan->tanggal_mulai),
                                                            tanggal_selesai: @js($penugasan->tanggal_selesai), --}}
                                                            status: @js($penugasan->status),
                                                        }
                                                    };

                                                    console.log('PAYLOAD KE MODAL:', payload);

                                                    $dispatch('open-smart-modal', payload);
                                                ">
                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg> Edit Penugasan
                                            </button>
                                        {{-- @endif --}}
                                    @endcan

                                    @can('send', $penugasan)
                                        {{-- @if($bolehAksi) --}}
                                            <!-- Tombol Buat Pengiriman -->
                                            <button
                                                class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 whitespace-nowrap border-b border-gray-100"
                                                @click="$dispatch('open-smart-modal', {
                                                    modalId: 'modal-pengiriman-anggota',
                                                    data: {
                                                        id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                        id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                        nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                    }
                                                })">
                                                <!-- icon -->
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Buat Pengiriman
                                            </button>
                                        {{-- @endif --}}
                                    @endcan

                                    <!-- Tombol Tampilkan Histori Pengiriman -->
                                    <button
                                        class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700
                                        hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 whitespace-nowrap border-b border-gray-100"
                                        @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-histori-pengiriman',
                                            data: {
                                                id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                historiData: @js($penugasan->pengirimans
                                                ->sortByDesc(fn($p) => $p->tanggal_pengiriman) // sort sebelum format
                                                ->map(
                                                    fn($p) => [
                                                        'tanggal_pengiriman' => $p->tanggal_pengiriman->format('d F Y'),
                                                        'jumlah_dikirim' => $p->jumlah_dikirim,
                                                        'media_pengiriman' => $p->media_pengiriman,
                                                        'bukti_dukung' => $p->bukti_dukung,
                                                        'status' => $p->penerimaan?->status ?? 'Belum Diproses',
                                                        'catatan' => $p->penerimaan?->catatan,
                                                    ],
                                                ),
                                            )}
                                        })">
                                        <!-- Icon -->
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                                        </svg>
                                        Tampilkan Histori Pengiriman
                                    </button>

                                    @can('receive', $penugasan)
                                        {{-- @if($bolehAksi) --}}
                                            <!-- Tombol Buat Penerimaan -->
                                            <button
                                                class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 whitespace-nowrap border-b border-gray-100"
                                                @click="$dispatch('open-smart-modal', {
                                                    modalId: 'modal-penerimaan-anggota',
                                                    data: {
                                                        id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                        id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                        id_pengiriman: '{{ $penugasan->latestPengiriman?->id_pengiriman }}',
                                                        nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                    }
                                                })">
                                                <!-- icon -->
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Buat Penerimaan
                                            </button>
                                        {{-- @endif --}}
                                    @endcan

                                    {{-- @if($penugasan->isDinasLuar()) --}}
                                    @can('acceptDL', $penugasan)
                                        <form action="{{ route('kalenderDL.store')}}"
                                            method="POST" class="flex flex-col items-center">
                                            @csrf

                                            <input type="hidden" name="id_pegawai" value="{{ $penugasan->id_anggota }}">
                                            <input type="hidden" name="tanggal_dl" value="{{ $penugasan->tanggal_selesai }}">

                                            <button type="submit" class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 flex items-center gap-2 whitespace-nowrap border-b border-gray-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Masukkan Kalender DL
                                            </button>
                                        </form>
                                    @endcan
                                    {{-- @endif --}}

                                    <!-- Tombol Jadikan CKP -->
                                    <button @click="closeDropdown()"
                                        class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 flex items-center gap-2 whitespace-nowrap">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Jadikan CKP</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-6 text-center text-gray-500">
                            Belum ada penugasan pada sub kegiatan ini
                        </td>
                    </tr>
                @endforelse
                <tr class="bg-gray-50">
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            Total
                        </td>
                        <td class="font-bold px-4 py-3 text-center text-xs text-gray-500 uppercase">
                            {{ $subKegiatan->penugasans->sum('target') }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">

                        </td>
                        <td colspan="3" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            {{ $totalKirim}}
                        </td>
                        <td colspan="3" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            {{ $totalTerima}}
                        </td>
                        <td colspan="2" class="px-4 py-3 text-sm text-gray-700 text-center">
                            <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer"
                                title="Kirim Ke Simket"
                                class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800">
                                Kirim ke Simket
                            </a>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Styling untuk kolom yang bisa di-toggle */
.section-pengiriman,
.section-penerimaan {
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

/* Ketika kolom disembunyikan */
.section-pengiriman.hidden,
.section-penerimaan.hidden {
    opacity: 0;
    max-width: 0;
    min-width: 0;
    width: 0;
    padding-left: 0;
    padding-right: 0;
    border: none;
    overflow: hidden;
}

/* Sticky untuk kolom tetap */
.sticky {
    position: sticky;
    z-index: 10;
}

.sticky.left-0 {
    left: 0;
}

.sticky.left-\[60px\] {
    left: 60px;
}

.sticky.right-0 {
    right: 0;
}

/* Animasi untuk tombol toggle */
.toggle-section-btn .rotate-90 {
    transform: rotate(90deg);
}

.toggle-section-btn .rotate-0 {
    transform: rotate(0deg);
}

/* Indikator visual untuk kolom yang hidden */
.section-pengiriman.hidden::after,
.section-penerimaan.hidden::after {
    content: "⋮";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6b7280;
    font-size: 20px;
    font-weight: bold;
    opacity: 0.5;
}

/* Border khusus untuk memisahkan section */
.border-l-2 {
    border-left-width: 2px;
}

/* Hover effect untuk kolom */
.section-pengiriman:hover {
    background-color: #f0f9ff;
}

.section-penerimaan:hover {
    background-color: #f0fdf4;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // State untuk tracking visibility
    const visibilityState = {
        pengiriman: true,
        penerimaan: true
    };
    
    // Elemen tombol toggle
    const togglePengirimanBtn = document.getElementById('toggle-pengiriman');
    const togglePenerimaanBtn = document.getElementById('toggle-penerimaan');
    const toggleSemuaBtn = document.getElementById('toggle-semua');
    
    // Fungsi untuk toggle section
    function toggleSection(section, show = null) {
        const shouldShow = show !== null ? show : !visibilityState[section];
        visibilityState[section] = shouldShow;
        
        // Toggle semua elemen dengan class section-[nama]
        const elements = document.querySelectorAll(`.section-${section}`);
        const button = document.querySelector(`[data-section="${section}"]`);
        
        elements.forEach(el => {
            if (shouldShow) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
        
        // Update icon tombol
        if (button) {
            const icon = button.querySelector('svg');
            if (icon) {
                if (shouldShow) {
                    icon.classList.remove('rotate-90');
                    icon.classList.add('rotate-0');
                } else {
                    icon.classList.remove('rotate-0');
                    icon.classList.add('rotate-90');
                }
            }
        }
        
        updateToggleButtons();
    }
    
    // Fungsi untuk update tampilan tombol
    function updateToggleButtons() {
        // Update teks tombol
        const sections = ['pengiriman', 'penerimaan'];
        sections.forEach(section => {
            const button = document.querySelector(`[data-section="${section}"]`);
            if (button) {
                button.classList.remove('bg-blue-100', 'text-blue-700', 'border-blue-300');
                if (visibilityState[section]) {
                    button.classList.add('bg-blue-100', 'text-blue-700', 'border-blue-300');
                }
            }
        });
    }
    
    // Event listener untuk toggle pengiriman
    togglePengirimanBtn.addEventListener('click', () => {
        toggleSection('pengiriman');
    });
    
    // Event listener untuk toggle penerimaan
    togglePenerimaanBtn.addEventListener('click', () => {
        toggleSection('penerimaan');
    });
    
    // Event listener untuk toggle semua
    toggleSemuaBtn.addEventListener('click', () => {
        const allVisible = Object.values(visibilityState).every(v => v);
        
        // Jika semua visible, sembunyikan semua
        // Jika ada yang hidden, tampilkan semua
        const shouldShow = !allVisible;
        
        toggleSection('pengiriman', shouldShow);
        toggleSection('penerimaan', shouldShow);
    });
    
    // Fungsi rating stars (tetap sama)
    function renderStars(container, rating) {
        container.innerHTML = '';
        for (let i = 0; i < rating; i++) {
            container.innerHTML += `
                <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            `;
        }
    }
    
    // Inisialisasi rating stars
    document.querySelectorAll('tr[data-target]').forEach(row => {
        // ... (kode rating stars tetap sama) ...
    });
    
    // Inisialisasi state awal
    updateToggleButtons();
});
</script>

