@php
    $loginUserId = auth()->user()->id_pegawai;

    // Helper untuk mengurutkan: user yang sedang login akan berada di paling atas
    $penugasanButuhDLAtauTranslok = $penugasanButuhDLAtauTranslok->sortByDesc(function ($row) use ($loginUserId) {
        return $row->id_anggota === $loginUserId ? 1 : 0;
    });

    $penugasanTidakButuhDLAtauTranslok = $penugasanTidakButuhDLAtauTranslok->sortByDesc(function ($row) use (
        $loginUserId,
    ) {
        return $row->id_anggota === $loginUserId ? 1 : 0;
    });
@endphp

<!-- Legenda Status Penugasan -->
<div class="mb-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 block">Legenda Status Penugasan (Garis Tepi
        Kiri):</h4>
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-gray-400"></div>
            <span class="text-xs text-gray-600 dark:text-gray-400">Menunggu Pengiriman</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
            <span class="text-xs text-gray-600 dark:text-gray-400">Menunggu Penerimaan</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-orange-400"></div>
            <span class="text-xs text-gray-600 dark:text-gray-400">Menunggu Pengiriman Ulang</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-red-400"></div>
            <span class="text-xs text-gray-600 dark:text-gray-400">Belum Diterima / Terlambat</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="text-xs text-gray-600 dark:text-gray-400">Tugas Selesai</span>
        </div>
    </div>
</div>

<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
            Perlu Dinas Luar
        </h3>
        <span
            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
            {{ $penugasanButuhDLAtauTranslok->count() }} Penugasan
        </span>
    </div>
    <div class="flex items-center gap-3 mb-3">
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="grid grid-cols-1">
                <div class="col-span-1 w-full overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    No.
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Jenis Kegiatan
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Target
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Waktu
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Status DL
                                </th>

                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Status Kirim
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Status Terima
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Detail
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($penugasanButuhDLAtauTranslok as $row)
                                @php
                                    $penugasan = $row;
                                    $isMe = $penugasan->id_anggota === $loginUserId;
                                    $statusClass = $penugasan->statusPenugasan()['class'];
                                    $isCkp = $penugasan->ckp ? true : false;

                                    // Ekstrak warna dari statusClass model
                                    $borderColor = 'border-l-transparent';
                                    if (str_contains($statusClass, 'bg-gray-100')) {
                                        $borderColor = 'border-l-gray-400';
                                    }
                                    if (str_contains($statusClass, 'bg-yellow-100')) {
                                        $borderColor = 'border-l-yellow-400';
                                    }
                                    if (str_contains($statusClass, 'bg-orange-100')) {
                                        $borderColor = 'border-l-orange-400';
                                    }
                                    if (str_contains($statusClass, 'bg-red-100')) {
                                        $borderColor = 'border-l-red-500';
                                    }
                                    if (str_contains($statusClass, 'bg-green-200')) {
                                        $borderColor = 'border-l-green-500';
                                    }

                                    // Jika sudah CKP, tambahkan class khusus
                                    $ckpClass = $isCkp ? 'ckp-completed-row' : '';
                                @endphp
                                <!-- Row Utama -->
                                <tr id="penugasan-{{ $penugasan->id_penugasan }}" class="border-l-4 {{ $borderColor }} {{ $isMe ? 'bg-blue-50/50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }} {{ $ckpClass }}">
                                    <td
                                        class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-800 dark:text-gray-300">
                                        <div class="font-medium">{{ $penugasan->anggota->nama_pegawai ?? '-' }}</div>
                                        @if ($penugasan->butuh_dl == 1 && $penugasan->butuh_translok == 0)
                                            <span
                                                class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                Dinas Luar
                                            </span>
                                        @elseif($penugasan->butuh_dl == 0 && $penugasan->butuh_translok == 1)
                                            <span
                                                class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 border border-teal-200 dark:border-teal-800">
                                                Translok
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400">
                                        {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                        <div class="font-semibold">{{ $penugasan->target ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $penugasan->satuan_target ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            {{ $row->tanggal_mulai && $row->tanggal_selesai
                                                ? ($row->tanggal_mulai->equalTo($row->tanggal_selesai)
                                                    ? $row->tanggal_mulai->translatedFormat('D, d M Y')
                                                    : $row->tanggal_mulai->translatedFormat('D, d M Y') .
                                                        ' - ' .
                                                        $row->tanggal_selesai->translatedFormat('D, d M Y'))
                                                : '-' }}
                                        </div>
                                    </td>

                                    {{-- BADGE STATUS DL --}}
                                    <td class="px-6 py-3 text-center">
                                        @php
                                            $dl = $penugasan->status_dl;
                                            $translok = $penugasan->status_translok;

                                            if ($dl === 'Ditolak' || $translok === 'Ditolak') {
                                                $status = 'Ditolak';
                                            } elseif ($dl === 'Menunggu' || $translok === 'Menunggu') {
                                                $status = 'Menunggu';
                                            } else {
                                                $status = 'ACC';
                                            }
                                        @endphp

                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            {{ $status === 'ACC'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : ($status === 'Ditolak'
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $penugasan->statusPengiriman()['class'] }}">
                                            {{ $penugasan->statusPengiriman()['label'] }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $penugasan->statusPenerimaan()['class'] }}">
                                            {{ $penugasan->statusPenerimaan()['label'] }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <div class="relative inline-block" x-data="{
                                            showDropdown: false,
                                            dropdownPosition: { x: 0, y: 0 },
                                            openDropdown(event) {
                                                const button = event.currentTarget;
                                                const rect = button.getBoundingClientRect();
                                                const dropdownWidth = 192;

                                                this.dropdownPosition = {
                                                    x: rect.left - dropdownWidth + 10,
                                                    y: rect.top - 10
                                                };
                                                this.showDropdown = true;
                                            },
                                            closeDropdown() {
                                                this.showDropdown = false;
                                            }}" x-on:mouseleave="closeDropdown()">

                                            <button x-on:mouseenter="openDropdown($event)"
                                                class="inline-flex items-center gap-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <svg class="fill-current" width="16" height="16"
                                                    viewBox="0 0 18 18" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                                </svg>
                                                Aksi
                                            </button>

                                            <div x-show="showDropdown" x-transition
                                                class="fixed z-[9999] bg-white dark:bg-gray-800 rounded shadow-xl border border-gray-200 dark:border-gray-700 min-w-[192px]"
                                                :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                                x-on:mouseenter="showDropdown = true" x-on:mouseleave="closeDropdown()">

                                                @can('update', $penugasan)
                                                    <button
                                                        class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                        @click="$dispatch('open-smart-modal', {
                                                            modalId: 'modal-penugasan-anggota',
                                                            mode: 'edit',
                                                            key: '{{ $penugasan->id_penugasan }}',
                                                            data: {
                                                                id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                                nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                                id_anggota: @js($penugasan->id_anggota),
                                                                nama_anggota: @js($penugasan->anggota?->nama_pegawai),
                                                                id_jenis_kegiatan: @js($penugasan->jenisKegiatan->id),
                                                                jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan),
                                                                target: @js($penugasan->target),
                                                                satuan_target: @js($penugasan->satuan_target),
                                                                butuh_dl: @js($penugasan->butuh_dl),
                                                                butuh_translok: @js($penugasan->butuh_translok),
                                                                tanggal_mulai: @js(optional($row->tanggal_mulai)->format('Y-m-d')),
                                                                tanggal_selesai: @js(optional($row->tanggal_selesai)->format('Y-m-d')),
                                                                status: @js($penugasan->status),
                                                                min_date: @js($subKegiatan->tanggal_mulai->format('Y-m-d')),
                                                                max_date: @js($subKegiatan->tanggal_selesai->format('Y-m-d'))
                                                            }
                                                        })">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit Penugasan
                                                    </button>
                                                @endcan

                                                <!-- @can('send', $penugasan)
                                                    <div class="relative group">
                                                        <button
                                                            class="w-full text-left px-4 py-3 text-sm flex items-center gap-2 border-b
                                                            {{ $penugasan->bolehKirimPenugasan()
                                                                ? 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                                                                : 'text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 cursor-not-allowed' }}"
                                                            {{ $penugasan->bolehKirimPenugasan() ? '' : 'disabled' }}
                                                            @if ($penugasan->bolehKirimPenugasan()) @click="$dispatch('open-smart-modal', {
                                                                modalId: 'modal-pengiriman-anggota',
                                                                data: {
                                                                    id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                                    id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                    nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                    target_penugasan: {{ $penugasan->target }},
                                                                    satuan_target: '{{ $penugasan->satuan_target ?? '' }}',
                                                                    tanggal_mulai: '{{ optional($penugasan->tanggal_mulai)->format('Y-m-d') }}',
                                                                }
                                                            })" @endif>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Buat Pengiriman
                                                        </button>

                                                        @if (!$penugasan->bolehKirimPenugasan() && $penugasan->tooltipPengirimanPenugasan())
                                                            @php
                                                                [$type, $text] = explode(
                                                                    '|',
                                                                    $penugasan->tooltipPengirimanPenugasan(),
                                                                    2,
                                                                );
                                                            @endphp
                                                            <div
                                                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block text-white text-xs rounded px-2 py-1
                                                                {{ $type === 'danger' ? 'bg-red-500/80' : ($type === 'warning' ? 'bg-orange-500/80' : 'bg-blue-500/80') }}">
                                                                {{ $text }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endcan -->

                                                <button
                                                    class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                    @click="$dispatch('open-smart-modal', {
                                                        modalId: 'modal-histori-pengiriman',
                                                        data: {
                                                            id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                            nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                            id_anggota: '{{ $penugasan->id_anggota }}',
                                                            historiData: @js(
                                                            $penugasan->pengirimans
                                                                ->sortBy(fn($p) => $p->created_at)
                                                                ->values()
                                                                ->map(
                                                                    fn($p, $idx) => [
                                                                        'id_pengiriman' => $p->id_pengiriman,
                                                                        'tanggal_pengiriman' => $p->tanggal_pengiriman->format('d F Y'),
                                                                        'jumlah_dikirim' => $p->jumlah_dikirim,
                                                                        'media_pengiriman' => $p->media_pengiriman,
                                                                        'bukti_dukung' => $p->bukti_dukung,
                                                                        'is_last' => $idx === $penugasan->pengirimans->count() - 1,
                                                                        'penerimaan' => [
                                                                            'id_penerimaan' => $p->penerimaan?->id_penerimaan,
                                                                            'id_penerima' => $p->penerimaan?->penerima?->nama_pegawai ?? 'Belum Diperiksa',
                                                                            'tanggal_penerimaan' => $p->penerimaan?->tanggal_penerimaan?->format('d F Y') ?? '-',
                                                                            'jumlah_diterima' => $p->penerimaan?->jumlah_diterima ?? '-',
                                                                            'status' => $p->penerimaan?->status ?? 'Menunggu',
                                                                            'catatan' => $p->penerimaan?->catatan ?? '-',
                                                                        ],
                                                                    ],
                                                                )
                                                                ->values(),
                                                        )}
                                                        })">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                                                    </svg>
                                                    Histori Pengiriman
                                                </button>

                                                <!-- @can('receive', $penugasan)
                                                    <div class="relative group">
                                                        <button
                                                            class="w-full text-left px-4 py-3 text-sm flex items-center gap-2 border-b
                                                            {{ $penugasan->bolehTerimaPenugasan()
                                                                ? 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                                                                : 'text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 cursor-not-allowed' }}"
                                                            {{ $penugasan->bolehTerimaPenugasan() ? '' : 'disabled' }}
                                                            @if ($penugasan->bolehTerimaPenugasan()) @click="$dispatch('open-smart-modal', {
                                                                modalId: 'modal-penerimaan-anggota',
                                                                data: {
                                                                    id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                                    id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                    id_pengiriman: '{{ $penugasan->latestPengiriman?->id_pengiriman }}',
                                                                    jumlah_pengiriman : '{{ $penugasan->latestPengiriman?->jumlah_dikirim }}',
                                                                    nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                    tanggal_mulai: '{{ optional($penugasan->tanggal_mulai)->format('Y-m-d') }}',
                                                                }
                                                            })" @endif>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Buat Penerimaan
                                                        </button>
                                                        @if (!$penugasan->bolehTerimaPenugasan() && $penugasan->tooltipPenerimaanPenugasan())
                                                            @php
                                                                [$type, $text] = explode(
                                                                    '|',
                                                                    $penugasan->tooltipPenerimaanPenugasan(),
                                                                    2,
                                                                );
                                                            @endphp
                                                            <div
                                                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block text-white text-xs rounded px-2 py-1
                                                                {{ $type === 'danger' ? 'bg-red-500/80' : ($type === 'warning' ? 'bg-orange-500/80' : 'bg-blue-500/80') }}">
                                                                {{ $text }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endcan -->

                                                <!-- @can('setAsCKP', $penugasan)
                                                    <button type="button"
                                                        @if(!$penugasan->ckp)
                                                            @click="$dispatch('open-ckp-modal', {
                                                                modalId: 'modal-ckp',
                                                                id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                nama_pegawai: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                uraian: 'Melaksanakan {{ $penugasan->jenisKegiatan->jenis_kegiatan }} pada {{ $penugasan->subKegiatan->nama_sub_kegiatan }}',
                                                                target_kuantitas: {{ $penugasan->target }},
                                                                satuan: '{{ $penugasan->satuan_target }}'
                                                            })"
                                                        @endif
                                                        class="w-full text-left px-4 py-3 text-sm flex items-center gap-2
                                                        {{ $penugasan->ckp ? 'text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800' : 'text-green-600 hover:bg-green-50' }}"
                                                        {{ $penugasan->ckp ? 'disabled' : '' }}>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                                            <path stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        {{ $penugasan->ckp ? 'Sudah jadi CKP' : 'Jadikan CKP' }}
                                                    </button>
                                                @endcan -->

                                                {{-- Delete --}}
                                                @can('delete', $penugasan)
                                                    <form id="delete-penugasan-{{ $penugasan->id_penugasan }}"
                                                        action="{{ route('penugasan.delete', [
                                                            'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                                                            'penugasan' => $penugasan->id_penugasan,
                                                        ]) }}"
                                                        method="POST">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="button"
                                                            onclick="SwalHelper.confirmDelete(
                                                                'delete-penugasan-{{ $penugasan->id_penugasan }}',
                                                                {{ json_encode($penugasan->anggota->nama_pegawai) }}
                                                            )"
                                                            class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 border-t">

                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" />
                                                            </svg>
                                                            Hapus Anggota
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <button x-data="{ isOpen: false }"
                                            @click="isOpen = !isOpen;
                                                    // Temukan detail row yang sesuai dan toggle
                                                    const detailRow = $el.closest('tr').nextElementSibling;
                                                    if (detailRow) {
                                                        const alpineData = Alpine.$data(detailRow);
                                                        if (alpineData) {
                                                            alpineData.showDetails = !alpineData.showDetails;
                                                        }
                                                    }"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 dark:bg-brand-900/30 dark:text-brand-400 dark:hover:bg-brand-800 dark:border-brand-700">
                                            <svg class="w-3 h-3" :class="{ 'rotate-180': isOpen }" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                            Detail
                                        </button>

                                        <!-- Badge TUGAS SELESAI -->
                                        @if($isCkp)
                                            <div class="ckp-completed-badge">
                                                TUGAS SELESAI
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Row Detail - Individual per row -->
                                <tr x-data="{ showDetails: false }" x-show="showDetails" x-cloak>
                                    <td colspan="11" class="px-0 py-4 bg-gray-50 dark:bg-gray-800">
                                        <div class="grid grid-cols-2 gap-4 px-6">
                                            <!-- PENGIRIMAN -->
                                            <div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        Pengiriman</h4>
                                                </div>

                                                <div
                                                    class="bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 p-4">
                                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->tanggal_pengiriman?->translatedFormat('D, d M Y') ?? 'Belum dikirim' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah Dikirim
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->jumlah_dikirim ?? '-' }} {{ $penugasan->satuan_target ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Media
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->media_pengiriman ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Response Rate</p>
                                                                <p
                                                                    class="text-base font-semibold text-blue-600 dark:text-blue-400">
                                                                    {{ $penugasan->latestPengiriman?->rr_kirim ?? 0 }}%
                                                                </p>
                                                            </div>

                                                            <div class="text-center">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Ketepatan Waktu</p>
                                                                <div x-data="{ show: false }" @mouseenter="show = true"
                                                                    @mouseleave="show = false"
                                                                    class="relative flex justify-center gap-0.5 cursor-pointer">
                                                                    {{-- BINTANG --}}
                                                                    @foreach ($penugasan->bintang_kirim_array as $filled)
                                                                        <span
                                                                            class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">
                                                                            ★
                                                                        </span>
                                                                    @endforeach

                                                                    {{-- TOOLTIP --}}
                                                                    <div x-show="show" x-transition
                                                                        class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">
                                                                        <div
                                                                            class="font-semibold text-yellow-400 mb-1">
                                                                            ⭐ Rating Pengiriman
                                                                        </div>

                                                                        <div class="space-y-1 text-gray-200">
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Nilai:</span>
                                                                                {{ $penugasan->latestPengiriman?->rating_kirim ?? 0 }}/5
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Deadline:</span>
                                                                                {{ optional($penugasan->tanggal_selesai)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Dikirim:</span>
                                                                                {{ optional($penugasan->latestPengiriman?->tanggal_pengiriman)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                        </div>

                                                                        {{-- ARROW --}}
                                                                        <div
                                                                            class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                            border-l-6 border-r-6 border-t-6
                                                                            border-l-transparent border-r-transparent border-t-gray-900">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="text-right">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Bukti</p>
                                                                <a href="{{ $penugasan->latestPengiriman?->bukti_dukung ?: '#' }}"
                                                                    target="_blank"
                                                                    class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                    Lihat
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PENERIMAAN -->
                                            <div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        Penerimaan</h4>
                                                </div>

                                                <div
                                                    class="bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 p-4">
                                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPenerimaan?->tanggal_penerimaan?->translatedFormat('D, d M Y') ?? 'Belum diterima' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah Diterima
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPenerimaan?->jumlah_diterima ?? '-' }} {{ $penugasan->satuan_target ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Media
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->media_pengiriman ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Response Rate</p>
                                                                <p
                                                                    class="text-base font-semibold text-green-600 dark:text-green-400">
                                                                    {{ $penugasan->latestPenerimaan?->rr_terima ?? 0 }}%
                                                                </p>
                                                            </div>

                                                            <div class="text-center">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Ketepatan Waktu</p>
                                                                <div x-data="{ show: false }" @mouseenter="show = true"
                                                                    @mouseleave="show = false"
                                                                    class="relative flex justify-center gap-0.5 cursor-pointer">
                                                                    {{-- BINTANG --}}
                                                                    @foreach ($penugasan->bintang_terima_array as $filled)
                                                                        <span
                                                                            class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">
                                                                            ★
                                                                        </span>
                                                                    @endforeach

                                                                    {{-- TOOLTIP --}}
                                                                    <div x-show="show" x-transition
                                                                        class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">
                                                                        <div
                                                                            class="font-semibold text-yellow-400 mb-1">
                                                                            ⭐ Rating Penerimaan
                                                                        </div>

                                                                        <div class="space-y-1 text-gray-200">
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Nilai:</span>
                                                                                {{ $penugasan->latestPenerimaan?->rating_terima ?? 0 }}/5
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Deadline:</span>
                                                                                {{ optional($penugasan->tanggal_selesai)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Diterima:</span>
                                                                                {{ optional($penugasan->latestPenerimaan?->tanggal_penerimaan)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                        </div>

                                                                        {{-- ARROW --}}
                                                                        <div
                                                                            class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                            border-l-6 border-r-6 border-t-6
                                                                            border-l-transparent border-r-transparent border-t-gray-900">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center">
                                        <div
                                            class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                            <p class="text-base font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                Belum ada penugasan</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500">Mulai dengan
                                                menambahkan penugasan baru</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Total Row -->
                            @php
                                $uniquePenugasanButuhDLAtauTranslok = $penugasanButuhDLAtauTranslok->unique('id_penugasan');
                                // ── Target ───────────────────────────────────────────────
                                $sumTargetDLAtauTranslok = $uniquePenugasanButuhDLAtauTranslok->sum('target');
                                // ── Response Rate Kirim ──────────────────────────────
                                $avgRrKirimDLAtauTranslok =
                                    $sumTargetDLAtauTranslok > 0
                                        ? round(($totalKirimButuhDLAtauTranslok / $sumTargetDLAtauTranslok) * 100, 2)
                                        : 0;
                                // ── Response Rate Terima ──────────────────────────────
                                $avgRrTerimaDLAtauTranslok =
                                    $sumTargetDLAtauTranslok > 0
                                        ? round(($totalTerimaButuhDLAtauTranslok / $sumTargetDLAtauTranslok) * 100, 2)
                                        : 0;

                                // ── Rating Kirim dan Bintangnya ──────────────────────────────────────────
                                $avgRatingKirimDLAtauTranslok =
                                    round($uniquePenugasanButuhDLAtauTranslok->avg(function ($p) {
                                        return $p->latestPengiriman?->rating_kirim ?? 0;
                                    }) ?? 0);
                                $bintangKirimDLAtauTranslokArray = array_map(function ($i) use ($avgRatingKirimDLAtauTranslok) {
                                    return $i <= round($avgRatingKirimDLAtauTranslok);
                                }, range(1, 5));

                                // ── Rating Terima dan Bintanganya ─────────────────────────────────────────
                                $avgRatingTerimaDLAtauTranslok =
                                    round($uniquePenugasanButuhDLAtauTranslok->avg(function ($p) {
                                        return $p->latestPenerimaan?->rating_terima ?? 0;
                                    }) ?? 0);
                                $bintangTerimaDLAtauTranslokArray = array_map(function ($i) use ($avgRatingTerimaDLAtauTranslok) {
                                    return $i <= round($avgRatingTerimaDLAtauTranslok);
                                }, range(1, 5));
                            @endphp
                            <tr class="bg-gray-50 dark:bg-gray-800 border-t-2 border-gray-200 dark:border-gray-700">
                                <td colspan="20" class="px-6 py-5">
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <!-- ================= SUMMARY ================= -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Ringkasan</h4>
                                            </div>
                                            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                                <div class="grid grid-cols-3 text-center gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-500">Target</p>
                                                        <p class="text-xl font-bold text-blue-600">
                                                            {{ $sumTargetDLAtauTranslok }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs text-gray-500">Pengiriman</p>
                                                        <p class="text-xl font-bold text-blue-600">
                                                            {{ $totalKirimButuhDLAtauTranslok }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs text-gray-500">Penerimaan</p>
                                                        <p class="text-xl font-bold text-green-600">
                                                            {{ $totalTerimaButuhDLAtauTranslok }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- ================= PENGIRIMAN ================= -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Pengiriman</h4>
                                            </div>

                                            <div
                                                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                                <div class="grid grid-cols-2 gap-4 text-center">

                                                    <!-- Response Rate -->
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Response Rate</p>
                                                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                                            {{ $avgRrKirimDLAtauTranslok }}%
                                                        </p>
                                                    </div>

                                                    <!-- Rating -->
                                                    {{-- <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                                                        <p
                                                            class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRatingKirimDLAtauTranslok }}
                                                        </p>
                                                    </div> --}}

                                                    <!-- Bintang Rating -->
                                                    <div class="text-center">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Ketepatan
                                                            Waktu</p>

                                                        <div x-data="{ show: false }" @mouseenter="show = true"
                                                            @mouseleave="show = false"
                                                            class="relative flex justify-center gap-0.5 cursor-pointer">

                                                            @foreach ($bintangKirimDLAtauTranslokArray as $filled)
                                                                <span
                                                                    class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">★</span>
                                                            @endforeach

                                                            <div x-show="show" x-transition
                                                                class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">

                                                                <div class="font-semibold text-yellow-400 mb-1">
                                                                    ⭐ Rata-rata Rating Pengiriman
                                                                </div>

                                                                <div class="space-y-1 text-gray-200">
                                                                    <div>
                                                                        <span class="text-gray-400">Rata-rata
                                                                            Nilai:</span>
                                                                        {{ $avgRatingKirimDLAtauTranslok }}/5
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                    border-l-6 border-r-6 border-t-6
                                                                    border-l-transparent border-r-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ================= PENERIMAAN ================= -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Penerimaan</h4>
                                            </div>

                                            <div
                                                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">

                                                <div class="grid grid-cols-2 gap-4 text-center">

                                                    <!-- Response Rate -->
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Response
                                                            Rate</p>
                                                        <p
                                                            class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRrTerimaDLAtauTranslok }}%
                                                        </p>
                                                    </div>

                                                    <!-- Rating -->
                                                    {{-- <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                                                        <p
                                                            class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRatingTerimaDLAtauTranslok }}
                                                        </p>
                                                    </div> --}}


                                                    <!-- Bintang Rating -->
                                                    <div class="text-center">

                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Ketepatan
                                                            Waktu</p>

                                                        <div x-data="{ show: false }" @mouseenter="show = true"
                                                            @mouseleave="show = false"
                                                            class="relative flex justify-center gap-0.5 cursor-pointer">

                                                            @foreach ($bintangTerimaDLAtauTranslokArray as $filled)
                                                                <span
                                                                    class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">★</span>
                                                            @endforeach

                                                            <div x-show="show" x-transition
                                                                class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">

                                                                <div class="font-semibold text-yellow-400 mb-1">
                                                                    ⭐ Rata-rata Rating Penerimaan
                                                                </div>

                                                                <div class="space-y-1 text-gray-200">
                                                                    <div>
                                                                        <span class="text-gray-400">Rata-rata
                                                                            Nilai:</span>
                                                                        {{ $avgRatingTerimaDLAtauTranslok }}/5
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                    border-l-6 border-r-6 border-t-6
                                                                    border-l-transparent border-r-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
            Tidak Perlu Dinas Luar
        </h3>
        <span
            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
            {{ $penugasanTidakButuhDLAtauTranslok->count() }} Penugasan
        </span>
    </div>
    <div class="flex items-center gap-3 mb-3">
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="grid grid-cols-1">
                <div class="col-span-1 w-full overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    No.
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Jenis Kegiatan
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Target
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Waktu
                                </th>

                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Status Kirim
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Status Terima
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                                    Detail
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($penugasanTidakButuhDLAtauTranslok as $row)
                                @php
                                    $penugasan = $row;
                                    $isMe = $penugasan->id_anggota === $loginUserId;
                                    $statusClass = $penugasan->statusPenugasan()['class'];
                                    $isCkp = $penugasan->ckp ? true : false;

                                    // Ekstrak warna dari statusClass model
                                    $borderColor = 'border-l-transparent';
                                    if (str_contains($statusClass, 'bg-gray-100')) {
                                        $borderColor = 'border-l-gray-400';
                                    }
                                    if (str_contains($statusClass, 'bg-yellow-100')) {
                                        $borderColor = 'border-l-yellow-400';
                                    }
                                    if (str_contains($statusClass, 'bg-orange-100')) {
                                        $borderColor = 'border-l-orange-400';
                                    }
                                    if (str_contains($statusClass, 'bg-red-100')) {
                                        $borderColor = 'border-l-red-500';
                                    }
                                    if (str_contains($statusClass, 'bg-green-200')) {
                                        $borderColor = 'border-l-green-500';
                                    }

                                    // Jika sudah CKP, tambahkan class khusus
                                    $ckpClass = $isCkp ? 'ckp-completed-row' : '';
                                @endphp
                                <!-- Row Utama -->
                                <tr id="penugasan-{{ $penugasan->id_penugasan }}" class="border-l-4 {{ $borderColor }} {{ $isMe ? 'bg-blue-50/50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }} {{ $ckpClass }}">
                                    <td
                                        class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-800 dark:text-gray-300">
                                        <div class="font-medium">{{ $penugasan->anggota->nama_pegawai ?? '-' }}</div>
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400">
                                        {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                        <div class="font-semibold">{{ $penugasan->target ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $penugasan->satuan_target ?? '-' }}</div>
                                    </td>

                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $row->tanggal_mulai && $row->tanggal_selesai
                                                ? ($row->tanggal_mulai->equalTo($row->tanggal_selesai)
                                                    ? $row->tanggal_mulai->translatedFormat('D, d M Y')
                                                    : $row->tanggal_mulai->translatedFormat('D, d M Y') .
                                                        ' - ' .
                                                        $row->tanggal_selesai->translatedFormat('D, d M Y'))
                                                : '-' }}
                                        </div>
                                    </td>



                                    <td class="px-6 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $penugasan->statusPengiriman()['class'] }}">
                                            {{ $penugasan->statusPengiriman()['label'] }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $penugasan->statusPenerimaan()['class'] }}">
                                            {{ $penugasan->statusPenerimaan()['label'] }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <div class="relative inline-block" x-data="{
                                            showDropdown: false,
                                            dropdownPosition: { x: 0, y: 0 },
                                            openDropdown(event) {
                                                const button = event.currentTarget;
                                                const rect = button.getBoundingClientRect();
                                                const dropdownWidth = 192;

                                                this.dropdownPosition = {
                                                    x: rect.left - dropdownWidth + 10,
                                                    y: rect.top - 10
                                                };
                                                this.showDropdown = true;
                                            },
                                            closeDropdown() {
                                                this.showDropdown = false;
                                            }
                                        }"
                                            x-on:mouseleave="closeDropdown()">

                                            <button x-on:mouseenter="openDropdown($event)"
                                                class="inline-flex items-center gap-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <svg class="fill-current" width="16" height="16"
                                                    viewBox="0 0 18 18" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                                </svg>
                                                Aksi
                                            </button>

                                            <div x-show="showDropdown" x-transition
                                                class="fixed z-[9999] bg-white dark:bg-gray-800 rounded shadow-xl border border-gray-200 dark:border-gray-700 min-w-[192px]"
                                                :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                                x-on:mouseenter="showDropdown = true"
                                                x-on:mouseleave="closeDropdown()">

                                                @can('update', $penugasan)
                                                    <button
                                                        class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                        @click="$dispatch('open-smart-modal', {
                                                            modalId: 'modal-penugasan-anggota',
                                                            mode: 'edit',
                                                            key: '{{ $penugasan->id_penugasan }}',
                                                            data: {
                                                                id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                                nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                                id_anggota: @js($penugasan->id_anggota),
                                                                nama_anggota: @js($penugasan->anggota?->nama_pegawai),
                                                                id_jenis_kegiatan: @js($penugasan->jenisKegiatan?->id),
                                                                jenis_kegiatan: @js($penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Jenis Kegiatan Terhapus'),
                                                                target: @js($penugasan->target),
                                                                satuan_target: @js($penugasan->satuan_target),
                                                                butuh_dl: @js($penugasan->butuh_dl),
                                                                butuh_translok: @js($penugasan->butuh_translok),
                                                                tanggal_mulai: @js(optional($row->tanggal_mulai)->format('Y-m-d')),
                                                                tanggal_selesai: @js(optional($row->tanggal_selesai)->format('Y-m-d')),
                                                                status: @js($penugasan->status),
                                                                min_date: @js($subKegiatan->tanggal_mulai->format('Y-m-d')),
                                                                max_date: @js($subKegiatan->tanggal_selesai->format('Y-m-d'))
                                                            }
                                                        })">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit Penugasan
                                                    </button>
                                                @endcan

                                                <!-- @can('send', $penugasan)
                                                    <div class="relative group">
                                                        <button
                                                            class="w-full text-left px-4 py-3 text-sm flex items-center gap-2 border-b
                                                            {{ $penugasan->bolehKirimPenugasan()
                                                                ? 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                                                                : 'text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 cursor-not-allowed' }}"
                                                            {{ $penugasan->bolehKirimPenugasan() ? '' : 'disabled' }}
                                                            @if ($penugasan->bolehKirimPenugasan()) @click="$dispatch('open-smart-modal', {
                                                                modalId: 'modal-pengiriman-anggota',
                                                                data: {
                                                                    id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                                    id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                    nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                    target_penugasan: {{ $penugasan->target }},
                                                                    satuan_target: '{{ $penugasan->satuan_target ?? '' }}',
                                                                    tanggal_mulai: '{{ optional($penugasan->tanggal_mulai)->format('Y-m-d') }}',
                                                                }
                                                            })" @endif>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Buat Pengiriman
                                                        </button>

                                                        @if (!$penugasan->bolehKirimPenugasan() && $penugasan->tooltipPengirimanPenugasan())
                                                            @php
                                                                [$type, $text] = explode(
                                                                    '|',
                                                                    $penugasan->tooltipPengirimanPenugasan(),
                                                                    2,
                                                                );
                                                            @endphp
                                                            <div
                                                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block text-white text-xs rounded px-2 py-1
                                                                {{ $type === 'danger' ? 'bg-red-500/80' : ($type === 'warning' ? 'bg-orange-500/80' : 'bg-blue-500/80') }}">
                                                                {{ $text }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endcan -->

                                                <button
                                                    class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                    @click="$dispatch('open-smart-modal', {
                                                        modalId: 'modal-histori-pengiriman',
                                                        data: {
                                                            id_sub_kegiatan: '{{ $subKegiatan->id_sub_kegiatan }}',
                                                            id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                            nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                            id_anggota: '{{ $penugasan->id_anggota }}',
                                                            historiData: @js(
                                                            $penugasan->pengirimans
                                                                ->sortByDesc(fn($p) => $p->created_at)
                                                                ->values()
                                                                ->map(
                                                                    fn($p, $idx) => [
                                                                        'id_pengiriman' => $p->id_pengiriman,
                                                                        'tanggal_pengiriman' => $p->tanggal_pengiriman->format('d F Y'),
                                                                        'jumlah_dikirim' => $p->jumlah_dikirim,
                                                                        'media_pengiriman' => $p->media_pengiriman,
                                                                        'bukti_dukung' => $p->bukti_dukung,
                                                                        'is_last' => $idx === 0,
                                                                        'penerimaan' => [
                                                                            'id_penerimaan' => $p->penerimaan?->id_penerimaan,
                                                                            'id_penerima' => $p->penerimaan?->penerima?->nama_pegawai ?? 'Belum Diperiksa',
                                                                            'tanggal_penerimaan' => $p->penerimaan?->tanggal_penerimaan?->format('d F Y') ?? '-',
                                                                            'jumlah_diterima' => $p->penerimaan?->jumlah_diterima ?? '-',
                                                                            'status' => $p->penerimaan?->status ?? 'Menunggu',
                                                                            'catatan' => $p->penerimaan?->catatan ?? '-',
                                                                        ],
                                                                    ],
                                                                )
                                                                ->values(),
                                                        )}
                                                        })">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                                                    </svg>
                                                    Histori Pengiriman
                                                </button>

                                                <!-- @can('receive', $penugasan)
                                                    <div class="relative group">
                                                        <button
                                                            class="w-full text-left px-4 py-3 text-sm flex items-center gap-2 border-b
                                                            {{ $penugasan->bolehTerimaPenugasan()
                                                                ? 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                                                                : 'text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 cursor-not-allowed' }}"
                                                            {{ $penugasan->bolehTerimaPenugasan() ? '' : 'disabled' }}
                                                            @if ($penugasan->bolehTerimaPenugasan()) @click="$dispatch('open-smart-modal', {
                                                                modalId: 'modal-penerimaan-anggota',
                                                                data: {
                                                                    id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                                    id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                    id_pengiriman: '{{ $penugasan->latestPengiriman?->id_pengiriman }}',
                                                                    jumlah_pengiriman : '{{ $penugasan->latestPengiriman?->jumlah_dikirim }}',
                                                                    nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                    tanggal_mulai: '{{ optional($penugasan->tanggal_mulai)->format('Y-m-d') }}',
                                                                }
                                                            })" @endif>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Buat Penerimaan
                                                        </button>
                                                        @if (!$penugasan->bolehTerimaPenugasan() && $penugasan->tooltipPenerimaanPenugasan())
                                                            @php
                                                                [$type, $text] = explode(
                                                                    '|',
                                                                    $penugasan->tooltipPenerimaanPenugasan(),
                                                                    2,
                                                                );
                                                            @endphp
                                                            <div
                                                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block text-white text-xs rounded px-2 py-1
                                                                {{ $type === 'danger' ? 'bg-red-500/80' : ($type === 'warning' ? 'bg-orange-500/80' : 'bg-blue-500/80') }}">
                                                                {{ $text }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endcan -->

                                                <!-- @can('setAsCKP', $penugasan)
                                                    <button type="button"
                                                        @if(!$penugasan->ckp)
                                                            @click="$dispatch('open-ckp-modal', {
                                                                modalId: 'modal-ckp',
                                                                id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                nama_pegawai: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                uraian: 'Melaksanakan {{ $penugasan->jenisKegiatan->jenis_kegiatan }} pada {{ $penugasan->subKegiatan->nama_sub_kegiatan }}',
                                                                target_kuantitas: {{ $penugasan->target }},
                                                                satuan: '{{ $penugasan->satuan_target }}'
                                                            })"
                                                        @endif
                                                        class="w-full text-left px-4 py-3 text-sm flex items-center gap-2
                                                        {{ $penugasan->ckp ? 'text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800' : 'text-green-600 hover:bg-green-50' }}"
                                                        {{ $penugasan->ckp ? 'disabled' : '' }}>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                                            <path stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        {{ $penugasan->ckp ? 'Sudah jadi CKP' : 'Jadikan CKP' }}
                                                    </button>
                                                @endcan -->

                                                {{-- Delete --}}
                                                @can('delete', $penugasan)
                                                    <form id="delete-penugasan-{{ $penugasan->id_penugasan }}"
                                                        action="{{ route('penugasan.delete', [
                                                            'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                                                            'penugasan' => $penugasan->id_penugasan,
                                                        ]) }}"
                                                        method="POST">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="button"
                                                            onclick="SwalHelper.confirmDelete(
                                                                'delete-penugasan-{{ $penugasan->id_penugasan }}',
                                                                {{ json_encode($penugasan->anggota->nama_pegawai) }}
                                                            )"
                                                            class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 border-t">

                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" />
                                                            </svg>
                                                            Hapus Anggota
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-3 text-center">
                                        <button x-data="{ isOpen: false }"
                                            @click="isOpen = !isOpen;
                                                    // Temukan detail row yang sesuai dan toggle
                                                    const detailRow = $el.closest('tr').nextElementSibling;
                                                    if (detailRow) {
                                                        const alpineData = Alpine.$data(detailRow);
                                                        if (alpineData) {
                                                            alpineData.showDetails = !alpineData.showDetails;
                                                        }
                                                    }"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 dark:bg-brand-900/30 dark:text-brand-400 dark:hover:bg-brand-800 dark:border-brand-700">
                                            <svg class="w-3 h-3" :class="{ 'rotate-180': isOpen }" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                            Detail
                                        </button>

                                        <!-- Badge TUGAS SELESAI -->
                                        @if($isCkp)
                                            <div class="ckp-completed-badge">
                                                TUGAS SELESAI
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Row Detail - Individual per row -->
                                <tr x-data="{ showDetails: false }" x-show="showDetails" x-cloak>
                                    <td colspan="10" class="px-0 py-4 bg-gray-50 dark:bg-gray-800">
                                        <div class="grid grid-cols-2 gap-4 px-6">
                                            <!-- PENGIRIMAN -->
                                            <div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        Pengiriman</h4>
                                                </div>

                                                <div
                                                    class="bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 p-4">
                                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->tanggal_pengiriman?->translatedFormat('D, d M Y') ?? 'Belum dikirim' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah Dikirim
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->jumlah_dikirim ?? '-' }} {{ $penugasan->satuan_target ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Media
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->media_pengiriman ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Response Rate</p>
                                                                <p
                                                                    class="text-base font-semibold text-blue-600 dark:text-blue-400">
                                                                    {{ $penugasan->latestPengiriman?->rr_kirim ?? 0 }}%
                                                                </p>
                                                            </div>

                                                            <div class="text-center">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Ketepatan Waktu</p>
                                                                <div x-data="{ show: false }"
                                                                    @mouseenter="show = true"
                                                                    @mouseleave="show = false"
                                                                    class="relative flex justify-center gap-0.5 cursor-pointer">
                                                                    {{-- BINTANG --}}
                                                                    @foreach ($penugasan->bintang_kirim_array as $filled)
                                                                        <span
                                                                            class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">
                                                                            ★
                                                                        </span>
                                                                    @endforeach

                                                                    {{-- TOOLTIP --}}
                                                                    <div x-show="show" x-transition
                                                                        class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">
                                                                        <div
                                                                            class="font-semibold text-yellow-400 mb-1">
                                                                            ⭐ Rating Pengiriman
                                                                        </div>

                                                                        <div class="space-y-1 text-gray-200">
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Nilai:</span>
                                                                                {{ $penugasan->latestPengiriman?->rating_kirim ?? 0 }}/5
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Deadline:</span>
                                                                                {{ optional($penugasan->tanggal_selesai)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Dikirim:</span>
                                                                                {{ optional($penugasan->latestPengiriman?->tanggal_pengiriman)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                        </div>

                                                                        {{-- ARROW --}}
                                                                        <div
                                                                            class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                            border-l-6 border-r-6 border-t-6
                                                                            border-l-transparent border-r-transparent border-t-gray-900">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="text-right">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Bukti</p>
                                                                <a href="{{ $penugasan->latestPengiriman?->bukti_dukung ?: '#' }}"
                                                                    target="_blank"
                                                                    class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                    Lihat
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PENERIMAAN -->
                                            <div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        Penerimaan</h4>
                                                </div>

                                                <div class="bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700 p-4">
                                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPenerimaan?->tanggal_penerimaan?->translatedFormat('D, d M Y') ?? 'Belum diterima' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah Diterima
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPenerimaan?->jumlah_diterima ?? '-' }} {{ $penugasan->satuan_target ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Media
                                                            </p>
                                                            <p
                                                                class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                {{ $penugasan->latestPengiriman?->media_pengiriman ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Response Rate</p>
                                                                <p
                                                                    class="text-base font-semibold text-green-600 dark:text-green-400">
                                                                    {{ $penugasan->latestPenerimaan?->rr_terima ?? 0 }}%
                                                                </p>
                                                            </div>

                                                            <div class="text-center">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Ketepatan Waktu</p>
                                                                <div x-data="{ show: false }"
                                                                    @mouseenter="show = true"
                                                                    @mouseleave="show = false"
                                                                    class="relative flex justify-center gap-0.5 cursor-pointer">
                                                                    {{-- BINTANG --}}
                                                                    @foreach ($penugasan->bintang_terima_array as $filled)
                                                                        <span
                                                                            class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">
                                                                            ★
                                                                        </span>
                                                                    @endforeach

                                                                    {{-- TOOLTIP --}}
                                                                    <div x-show="show" x-transition
                                                                        class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">
                                                                        <div
                                                                            class="font-semibold text-yellow-400 mb-1">
                                                                            ⭐ Rating Penerimaan
                                                                        </div>

                                                                        <div class="space-y-1 text-gray-200">
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Nilai:</span>
                                                                                {{ $penugasan->latestPenerimaan?->rating_terima ?? 0 }}/5
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Deadline:</span>
                                                                                {{ optional($penugasan->tanggal_selesai)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                            <div>
                                                                                <span
                                                                                    class="text-gray-400">Diterima:</span>
                                                                                {{ optional($penugasan->latestPenerimaan?->tanggal_penerimaan)->translatedFormat('d M Y') ?? '-' }}
                                                                            </div>
                                                                        </div>

                                                                        {{-- ARROW --}}
                                                                        <div
                                                                            class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                            border-l-6 border-r-6 border-t-6
                                                                            border-l-transparent border-r-transparent border-t-gray-900">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center">
                                        <div
                                            class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                            <p class="text-base font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                Belum ada penugasan</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500">Mulai dengan
                                                menambahkan penugasan baru</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Total Row -->
                            @php
                                $uniquePenugasanTidakButuhDLAtauTranslok = $penugasanTidakButuhDLAtauTranslok->unique(
                                    'id_penugasan',
                                );
                                // ── Target ───────────────────────────────────────────────
                                $sumTargetNonDLAtauTranslok = $uniquePenugasanTidakButuhDLAtauTranslok->sum('target');
                                // ── Response Rate Kirim ──────────────────────────────
                                $avgRrKirimNonDLAtauTranslok =
                                    $sumTargetNonDLAtauTranslok > 0
                                    ? round(($totalKirimTidakButuhDLAtauTranslok / $sumTargetNonDLAtauTranslok) * 100, 2)
                                    : 0; 
                                // ── Response Rate Terima ──────────────────────────────
                                $avgRrTerimaNonDLAtauTranslok =
                                    $sumTargetNonDLAtauTranslok > 0
                                    ? round(($totalTerimaTidakButuhDLAtauTranslok / $sumTargetNonDLAtauTranslok) * 100, 2)
                                    : 0; 
                                
                                // ── Rating Kirim dan Bintangnya ──────────────────────────────────────────
                                $avgRatingKirimNonDLAtauTranslok =
                                    round($uniquePenugasanTidakButuhDLAtauTranslok->avg(function ($p) {
                                        return $p->latestPengiriman?->rating_kirim ?? 0; 
                                    }) ?? 0);
                                $bintangKirimNonDLAtauTranslokArray = array_map(function ($i) use ($avgRatingKirimNonDLAtauTranslok) {
                                    return $i <= round($avgRatingKirimNonDLAtauTranslok);
                                }, range(1, 5));

                                // ── Rating Terima dan Bintanganya ─────────────────────────────────────────
                                $avgRatingTerimaNonDLAtauTranslok =
                                    round($uniquePenugasanTidakButuhDLAtauTranslok->avg(function ($p) {
                                        return $p->latestPenerimaan?->rating_terima ?? 0; 
                                    }) ?? 0);
                                $bintangTerimaNonDLAtauTranslokArray = array_map(function ($i) use ($avgRatingTerimaNonDLAtauTranslok) {
                                    return $i <= round($avgRatingTerimaNonDLAtauTranslok);
                                }, range(1, 5));

                                // @dd([
                                //     'uniquePenugasanTidakButuhDLAtauTranslok' => $uniquePenugasanTidakButuhDLAtauTranslok->map(fn($p) => [
                                //         'id_penugasan' => $p->id_penugasan,
                                //         'latestPengiriman' => $p->latestPengiriman?->toArray(),
                                //         'latestPenerimaan' => $p->latestPenerimaan?->toArray(),
                                //         'rating_kirim' => $p->latestPengiriman?->rating_kirim,
                                //         'rating_terima' => $p->latestPenerimaan?->rating_terima,
                                //     ]),
                                //     'avg_rating_kirim_mentah' => $uniquePenugasanTidakButuhDLAtauTranslok->avg(fn($p) => $p->latestPengiriman?->rating_kirim ?? 0),
                                //     'avg_rating_terima_mentah' => $uniquePenugasanTidakButuhDLAtauTranslok->avg(fn($p) => $p->latestPenerimaan?->rating_terima ?? 0),
                                //     'sumTargetNonDLAtauTranslok' => $sumTargetNonDLAtauTranslok,
                                //     'totalKirimTidakButuhDLAtauTranslok' => $totalKirimTidakButuhDLAtauTranslok,
                                //     'avgRrKirimNonDLAtauTranslok' => $avgRrKirimNonDLAtauTranslok,
                                //     'avgRatingKirimNonDLAtauTranslok' => $avgRatingKirimNonDLAtauTranslok,
                                //     'bintangKirimNonDLAtauTranslokArray' => $bintangKirimNonDLAtauTranslokArray,
                                //     'totalTerimaTidakButuhDLAtauTranslok' => $totalTerimaTidakButuhDLAtauTranslok,
                                //     'avgRrTerimaNonDLAtauTranslok' => $avgRrTerimaNonDLAtauTranslok,
                                //     'avgRatingTerimaNonDLAtauTranslok' => $avgRatingTerimaNonDLAtauTranslok,
                                //     'bintangTerimaNonDLAtauTranslokArray' => $bintangTerimaNonDLAtauTranslokArray,
                                // ])
                            @endphp
                            <tr class="bg-gray-50 dark:bg-gray-800 border-t-2 border-gray-200 dark:border-gray-700">
                                <td colspan="20" class="px-6 py-5">
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <!-- ================= SUMMARY ================= -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Total</h4>
                                            </div>
                                            <div
                                                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                                <div class="grid grid-cols-3 text-center gap-4">

                                                    <div>
                                                        <p class="text-xs text-gray-500">Target Penugasan</p>
                                                        <p class="text-xl font-bold text-blue-600">
                                                            {{ $sumTargetNonDLAtauTranslok }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs text-gray-500">Jumlah Dikirim</p>
                                                        <p class="text-xl font-bold text-orange-600">
                                                            {{ $totalKirimTidakButuhDLAtauTranslok }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs text-gray-500">Jumlah Diterima</p>
                                                        <p class="text-xl font-bold text-green-600">
                                                            {{ $totalTerimaTidakButuhDLAtauTranslok }}
                                                        </p>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <!-- ================= PENGIRIMAN ================= -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Pengiriman</h4>
                                            </div>

                                            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                                <div class="grid grid-cols-2 gap-4 text-center">

                                                    <!-- Response Rate -->
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Response
                                                            Rate</p>
                                                        <p
                                                            class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRrKirimNonDLAtauTranslok }}%
                                                        </p>
                                                    </div>

                                                    <!-- Rating -->
                                                    {{-- <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                                                        <p
                                                            class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRatingKirimNonDLAtauTranslok }}
                                                        </p>
                                                    </div> --}}

                                                    <!-- Bintang Rating -->
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Ketepatan
                                                            Waktu</p>

                                                        <div x-data="{ show: false }" @mouseenter="show = true"
                                                            @mouseleave="show = false"
                                                            class="relative flex justify-center gap-0.5 cursor-pointer">

                                                            @foreach ($bintangKirimNonDLAtauTranslokArray as $filled)
                                                                <span
                                                                    class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">★</span>
                                                            @endforeach

                                                            <div x-show="show" x-transition
                                                                class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">

                                                                <div class="font-semibold text-yellow-400 mb-1">
                                                                    ⭐ Rata-rata Rating Pengiriman
                                                                </div>

                                                                <div class="space-y-1 text-gray-200">
                                                                    <div>
                                                                        <span class="text-gray-400">Rata-rata
                                                                            Nilai : </span>
                                                                        {{ $avgRatingKirimNonDLAtauTranslok }}/5
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                    border-l-6 border-r-6 border-t-6
                                                                    border-l-transparent border-r-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- ================= PENERIMAAN ================= -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Penerimaan</h4>
                                            </div>

                                            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                                <div class="grid grid-cols-2 gap-4 text-center">

                                                    <!-- Response Rate -->
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Response
                                                            Rate</p>
                                                        <p
                                                            class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRrTerimaNonDLAtauTranslok }}%
                                                        </p>
                                                    </div>

                                                    <!-- Rating -->
                                                    {{-- <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                                                        <p class="text-lg text-center font-semibold text-green-600 dark:text-green-400">
                                                            {{ $avgRatingTerimaNonDLAtauTranslok }}
                                                        </p>
                                                    </div> --}}

                                                    <!-- Bintang Rating -->
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Ketepatan
                                                            Waktu</p>

                                                        <div x-data="{ show: false }" @mouseenter="show = true"
                                                            @mouseleave="show = false"
                                                            class="relative flex justify-center gap-0.5 cursor-pointer">

                                                            @foreach ($bintangTerimaNonDLAtauTranslokArray as $filled)
                                                                <span
                                                                    class="{{ $filled ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-500' }}">★</span>
                                                            @endforeach

                                                            <div x-show="show" x-transition
                                                                class="absolute z-50 bottom-full mb-2 w-56 rounded-lg bg-gray-900 text-white text-xs shadow-lg px-3 py-2">

                                                                <div class="font-semibold text-yellow-400 mb-1">
                                                                    ⭐ Rata-rata Rating Penerimaan
                                                                </div>

                                                                <div class="space-y-1 text-gray-200">
                                                                    <div>
                                                                        <span class="text-gray-400">Rata-rata
                                                                            Nilai : </span>
                                                                        {{ $avgRatingTerimaNonDLAtauTranslok }}/5
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0
                                                                    border-l-6 border-r-6 border-t-6
                                                                    border-l-transparent border-r-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

