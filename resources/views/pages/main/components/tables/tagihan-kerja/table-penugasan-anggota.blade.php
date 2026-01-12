<div class="border border-gray-200 rounded-lg overflow-hidden">
    <div class="grid grid-cols-1">
        <div class="col-span-1 overflow-x-auto">
            <table class="max-w-[1400px] w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            No.
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Nama
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Jenis Kegiatan
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Target
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Deadline
                        </th>
                        <th colspan="3"
                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            Pengiriman
                        </th>
                        <th colspan="4"
                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            Penerimaan
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            Aksi
                        </th>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Detail
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            RR (%)
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Ketepatan Waktu
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Detail
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            RR (%)
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            Ketepatan Waktu
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
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
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-center">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                {{ $penugasan->target ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                {{ $penugasan->tanggal_selesai->translatedFormat('D, d M Y') ?? '-' }}
                            </td>

                            {{-- PENGIRIMAN --}}
                            <td class="px-4 py-3 text-sm text-gray-700">
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

                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                {{ $penugasan->rr_kirim ? $penugasan->rr_kirim . '%' : '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                <div class="flex justify-center">
                                    <div class="flex justify-center rating-kirim"></div>
                                </div>
                            </td>

                            {{-- PENERIMAAN --}}
                            <td class="px-4 py-3 text-sm text-gray-700">
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

                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                {{ $penugasan->rr_terima ? $penugasan->rr_terima . '%' : '-' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                <div class="flex justify-center">
                                    <div class="flex justify-center rating-terima"></div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700 text-center">
                                <a href="{{ $penugasan->latestPengiriman?->bukti_dukung ?: 'https://www.youtube.com/' }}"
                                    target="_blank" rel="noopener noreferrer"
                                    title="{{ $penugasan->bukti_dukung ? 'Buka bukti dukung' : 'Belum ada bukti dukung' }}"
                                    class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800">
                                    {{ $penugasan->latestPengiriman?->bukti_dukung ? 'Lihat Bukti' : 'Belum Ada' }}
                                </a>
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-gray-700 text-center border-r border-gray-200">
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
                                            {{-- Edit Data Penugasan --}}
                                            <button
                                                class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2 whitespace-nowrap border-b border-gray-100"
                                                @click="$dispatch('open-smart-modal', {
                                                modalId: 'modal-penugasan-anggota',
                                                mode: 'edit',
                                                key: '{{ $penugasan->id_penugasan }}',
                                                data: {
                                                    id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                    nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                    id_anggota: @js($penugasan->id_anggota),
                                                    nama_anggota: @js($penugasan->anggota?->nama_pegawai),
                                                    target: @js($penugasan->target),
                                                    tanggal_mulai: @js($penugasan->tanggal_mulai),
                                                    tanggal_selesai: @js($penugasan->tanggal_selesai),
                                                    status: @js($penugasan->status),
                                                }

                                            })">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg> Edit Penugasan
                                            </button>
                                        @endcan

                                        @can('send', $penugasan)
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
                                        @endcan

                                        @can('viewHistory', $penugasan)
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
                                                                'catatan' => $p->penerimaan?->catatan ?? '--',
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
                                        @endcan

                                        @can('receive', $penugasan)
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
                                        @endcan

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
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('tr[data-target]').forEach(row => {
        const target = Number(row.dataset.target);
        const deadline = new Date(row.dataset.deadline);

        // =========================
        // PENGIRIMAN
        // =========================
        const jumlahKirim = Number(row.dataset.kirimJumlah);
        const tanggalKirim = row.dataset.kirimTanggal
            ? new Date(row.dataset.kirimTanggal)
            : null;

        const kirimContainer = row.querySelector('.rating-kirim');

        if (!jumlahKirim || !tanggalKirim) {
            kirimContainer.textContent = '-';
        } else {
            let rating = 1;
            const sesuaiTarget = jumlahKirim === target;
            const tepatWaktu = tanggalKirim <= deadline;

            if (sesuaiTarget && tepatWaktu) rating = 5;
            else if (sesuaiTarget) rating = 4;
            else if (tepatWaktu) rating = 3;
            else rating = 2;

            renderStars(kirimContainer, rating);
        }

        // =========================
        // PENERIMAAN
        // =========================
        const jumlahTerima = Number(row.dataset.terimaJumlah);
        const tanggalTerima = row.dataset.terimaTanggal
            ? new Date(row.dataset.terimaTanggal)
            : null;

        const terimaContainer = row.querySelector('.rating-terima');

        if (!jumlahTerima || !tanggalTerima) {
            terimaContainer.textContent = '-';
        } else {
            let rating = 1;
            const sesuaiJumlah = jumlahTerima === jumlahKirim;
            const tepatWaktu = tanggalTerima <= deadline;

            if (sesuaiJumlah && tepatWaktu) rating = 5;
            else if (sesuaiJumlah) rating = 4;
            else if (tepatWaktu) rating = 3;
            else rating = 2;

            renderStars(terimaContainer, rating);
        }
    });
});

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
</script>



