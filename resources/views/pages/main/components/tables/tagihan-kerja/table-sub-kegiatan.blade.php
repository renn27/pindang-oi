<!-- TABLE Sub Kegiatan -->
<div class="grid grid-cols-1">
    <div class="col-span-1 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th scope="col"
                        class="pl-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                        No.
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Nama Sub Kegiatan
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                        Anggota
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Tanggal Mulai
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Tanggal Selesai
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-48">
                        Progres Tugas
                    </th>
                    @if(auth()->user()->active_role !== 'Anggota Tim')
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">
                            Aksi
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($kegiatan->subKegiatans as $index => $subKegiatan)
                    @php
                        $totalPenugasan = $subKegiatan->penugasans->count();
                        $totalPenugasanSelesai = $subKegiatan->penugasans->filter(function($p) {
                            return $p->pengirimans->contains(function ($pengiriman) {
                                return $pengiriman->penerimaan?->status === 'Diterima';
                            }); 
                        })->count(); 

                        $totalTargetPenugasan = $subKegiatan->penugasans->sum('target');
                        $penugasanTargetSelesai = $subKegiatan->penugasans->sum(function($p) {
                            $adaPelunasan = $p->pengirimans->contains(fn($k) =>
                                $k->tipe_pengiriman === 'Pelunasan' && $k->penerimaan?->status === 'Diterima'
                            );

                            return $p->pengirimans->sum(fn($k) =>
                                $k->penerimaan?->status === 'Diterima' &&
                                $k->tipe_pengiriman === ($adaPelunasan ? 'Pelunasan' : 'Cicilan')
                                    ? $k->jumlah_dikirim ?? 0
                                    : 0
                            );
                        });

                        $progressPercent = $totalTargetPenugasan ? round(($penugasanTargetSelesai / $totalTargetPenugasan) * 100) : 0;

                        // Tentukan warna progress bar
                        $progressColor = $progressPercent >= 100 ? 'bg-green-500' : 'bg-blue-500';
                        $progressTextColor = $progressPercent >= 100 ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400';

                        $startMonth = \Carbon\Carbon::parse($subKegiatan->tanggal_mulai)->startOfMonth();
                        $endMonth = \Carbon\Carbon::parse($subKegiatan->tanggal_selesai)->startOfMonth();
                        $totalBulan = $startMonth->diffInMonths($endMonth) + 1;
                        
                        $ckpSelesai100Persen = ($progressPercent >= 100) && $subKegiatan->ckpBulanan->contains(function ($ckp) use ($totalTargetPenugasan) {
                            return $ckp->realisasi >= $totalTargetPenugasan;
                        });

                        $isCkpKetuaTim = $ckpSelesai100Persen || $subKegiatan->ckpBulanan->count() >= $totalBulan;
                    @endphp

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $isCkpKetuaTim ? 'bg-green-100/50 hover:bg-green-100/80 dark:bg-green-900/50 hover:dark:bg-green-900/80' : '' }}">
                        {{-- Nomor --}}
                        <td class="pl-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300 text-center align-top">
                            {{ $index + 1 }}
                        </td>

                        {{-- Nama Sub Kegiatan + Progress --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-col gap-2">
                                {{-- Nama Sub Kegiatan (Font Lebih Besar) --}}
                                <a href="{{ route('sub.kegiatan.show', [
                                    'kegiatan' => $kegiatan->id_kegiatan,
                                    'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                                ]) }}"
                                    title="Lihat detail sub kegiatan"
                                    class="text-sm font-semibold text-gray-600 hover:text-blue-600 dark:text-white dark:hover:text-blue-400 hover:underline transition-colors">
                                    {{ $subKegiatan->nama_sub_kegiatan }}
                                </a>

                                {{-- Badge CKP Ketua Tim --}}
                                @if($isCkpKetuaTim)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 w-fit">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        CKP Ketua Tim
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Jumlah Anggota --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <span class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-full bg-blue-50 dark:bg-blue-900/30 text-sm font-medium text-blue-700 dark:text-blue-400">
                                {{ $totalPenugasan }}
                            </span>
                        </td>

                        {{-- Tanggal Mulai --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $subKegiatan->tanggal_mulai->translatedFormat('d M Y') }}
                            </span>
                        </td>

                        {{-- Tanggal Selesai --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $subKegiatan->tanggal_selesai->translatedFormat('d M Y') }}
                            </span>
                        </td>

                        {{-- Progress Bar --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 max-w-[300px] h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
                                    <div class="h-full {{ $progressColor }} rounded-full transition-all duration-500"
                                        style="width: {{ $progressPercent }}%">
                                    </div>
                                </div>
                                <span class="text-xs font-medium {{ $progressTextColor }}">
                                    {{ $progressPercent }}%
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    ({{ $penugasanTargetSelesai }}/{{ $totalTargetPenugasan }})
                                </span>
                            </div>
                        </td>

                        {{-- Aksi --}}
                        @if(auth()->user()->active_role !== 'Anggota Tim')
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="flex justify-center items-center gap-1">
                                @if(!$ckpSelesai100Persen)
                                    {{-- Edit --}}
                                    @can('update', $subKegiatan)
                                        <button class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                            @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-sub-kegiatan',
                                            mode: 'edit',
                                            key: '{{ $subKegiatan->id_sub_kegiatan }}',
                                            data: {
                                                id_kegiatan: @js($kegiatan->id_kegiatan),
                                                nama_rk_kegiatan: @js($kegiatan->nama_rk_kegiatan),
                                                id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                target: @js($subKegiatan->target),
                                                satuan_target: @js($subKegiatan->satuan_target),
                                                tanggal_mulai: @js(optional($subKegiatan->tanggal_mulai)->format('Y-m-d')),
                                                tanggal_selesai: @js(optional($subKegiatan->tanggal_selesai)->format('Y-m-d')),
                                                status: @js($subKegiatan->status),
                                            }
                                        })"
                                            title="Edit Sub Kegiatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endcan

                                    {{-- Delete --}}
                                    @can('delete', $subKegiatan)
                                        @if($totalPenugasanSelesai > 0)
                                            <button disabled
                                                title="Sub Kegiatan sudah berjalan&#10;sehingga tidak dapat dihapus"
                                                class="p-2 text-red-400 dark:text-red-500 bg-red-50/50 dark:bg-red-900/20 rounded-lg cursor-not-allowed opacity-70 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @else
                                            <form id="delete-sub-kegiatan-{{ $subKegiatan->id_sub_kegiatan }}"
                                                action="{{ route('sub.kegiatan.delete', [
                                                    'kegiatan' => $kegiatan->id_kegiatan,
                                                    'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                                                ]) }}"
                                                method="POST" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="SwalHelper.confirmDelete(
                                                        'delete-sub-kegiatan-{{ $subKegiatan->id_sub_kegiatan }}',
                                                        {{ json_encode($subKegiatan->nama_sub_kegiatan) }}
                                                    )"
                                                    class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                                    title="Hapus Sub Kegiatan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                @endif

                                {{-- Jadikan CKP --}}
                                @if(auth()->user()->active_role === 'Ketua Tim' && auth()->user()->id_pegawai === $subKegiatan->kegiatan->id_penanggung_jawab)
                                    @if($isCkpKetuaTim)
                                        {{-- Sudah jadi CKP --}}
                                        <button disabled
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs font-medium cursor-not-allowed"
                                            title="{{ $ckpSelesai100Persen ? 'Semua target sudah jadi CKP' : 'Sudah jadi CKP Ketua Tim' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>{{ $ckpSelesai100Persen ? 'Sudah CKP Semua' : 'Sudah CKP' }}</span>
                                        </button>
                                    @elseif($penugasanTargetSelesai >= 1)
                                        {{-- Selesai, bisa dijadikan CKP --}}
                                        <button type="button"
                                            @click="$dispatch('open-smart-modal', {
                                                modalId: 'modal-ckp-universal',
                                                data: {
                                                    id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                    nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                    nama_pegawai: @js($subKegiatan->kegiatan->penanggungJawab->nama_pegawai),
                                                    uraian: @js('Melaksanakan dan Mengetuai ' . $subKegiatan->nama_sub_kegiatan . ' dengan target ' . $penugasanTargetSelesai . ' dari total target ' . $totalTargetPenugasan),
                                                    target_kuantitas: {{ $totalTargetPenugasan }},
                                                    realisasi_kuantitas: {{ $penugasanTargetSelesai }},
                                                    satuan: @js($subKegiatan->satuan_target),
                                                    is_ketua_tim: true,
                                                    tanggal_mulai: @js($subKegiatan->tanggal_mulai->format('Y-m-d')),
                                                    tanggal_selesai: @js($subKegiatan->tanggal_selesai->format('Y-m-d')),
                                                    bulanSudahCkp: @js($subKegiatan->ckpBulanan->pluck('bulan_ckp')->values()->toArray())
                                                }
                                            })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-green-200 dark:border-green-700 bg-white dark:bg-transparent text-gray-400 dark:text-gray-500 text-xs font-medium hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-700 dark:hover:text-green-400 hover:border-green-300 dark:hover:border-green-700 active:scale-95 transition-all duration-150"
                                            title="Jadikan CKP Ketua Tim">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Buat CKP</span>
                                        </button>
                                    @else
                                        {{-- Belum selesai, tidak bisa diklik --}}
                                        <button disabled
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-transparent text-gray-400 dark:text-gray-500 text-xs font-medium cursor-not-allowed opacity-60"
                                            title="{{ $totalPenugasan == 0 ? 'Belum ada penugasan' : 'Belum ada Pengiriman yang diterima saat ini (' . $progressPercent . '%)' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Belum CKP</span>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                </div>
                                <p class="text-base font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Belum ada Sub Kegiatan
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Mulai dengan menambahkan sub kegiatan baru
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
