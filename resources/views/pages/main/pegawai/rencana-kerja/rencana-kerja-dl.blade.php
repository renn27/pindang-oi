@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

    <x-ui.smart-modal id="modal-verifikasi-translok" class="max-w-md" @open-smart-modal.window="
                if ($event.detail.modalId !== 'modal-verifikasi-translok') return;

                itemKey = $event.detail.key ?? null;

                Object.assign(formData, $event.detail.data ?? {});
            ">
        <form :action="`/penugasan/${itemKey}/rencana-kerja-translok`" method="POST" class="grid grid-cols-1 gap-y-4">
            @csrf
            @method('PUT')

            <div class="rounded-3xl bg-white dark:bg-gray-800">

                <!-- HEADER -->
                <div class="border-b dark:border-gray-700 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Verifikasi Translok
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Review singkat sebelum mengambil keputusan
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 space-y-3 text-sm text-gray-700 dark:text-gray-400">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Nama Pegawai :</span><br>
                        <span x-text="formData.nama_pegawai"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Jenis Kegiatan :</span><br>
                        <span x-text="formData.jenis_kegiatan"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Waktu Pelaksanaan :</span><br>
                        <span x-text="formData.tanggal_mulai"></span>
                        s.d.
                        <span x-text="formData.tanggal_selesai"></span>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="border-t dark:border-gray-700 px-6 py-4 flex justify-end gap-2">
                    <button type="button" @click="open = false"
                        class="rounded-lg border dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button type="submit" name="status_translok" value="Ditolak"
                            class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm text-white">
                            Tolak
                        </button>

                        <button type="submit" name="status_translok" value="ACC"
                            class="rounded-lg bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button type="submit" name="status_translok" value="Menunggu"
                            class="rounded-lg bg-orange-600 hover:bg-orange-700 px-4 py-2 text-sm text-white">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <x-ui.smart-modal id="modal-verifikasi-dl" class="max-w-md" @open-smart-modal.window="
                if ($event.detail.modalId !== 'modal-verifikasi-dl') return;

                itemKey = $event.detail.key ?? null;

                Object.assign(formData, $event.detail.data ?? {});
            ">
        <form :action="`/penugasan/${itemKey}/rencana-kerja-dl`" method="POST" class="grid grid-cols-1 gap-y-4">
            @csrf
            @method('PUT')

            <div class="rounded-3xl bg-white dark:bg-gray-800">

                <!-- HEADER -->
                <div class="border-b dark:border-gray-700 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Verifikasi Dinas Luar
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Review singkat sebelum mengambil keputusan
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 space-y-3 text-sm text-gray-700 dark:text-gray-400">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Nama Pegawai:</span><br>
                        <span x-text="formData.nama_pegawai"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Jenis Kegiatan:</span><br>
                        <span x-text="formData.jenis_kegiatan"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Waktu Pelaksanaan:</span><br>
                        <span x-text="formData.tanggal_mulai"></span>
                        s.d
                        <span x-text="formData.tanggal_selesai"></span>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="border-t dark:border-gray-700 px-6 py-4 flex justify-end gap-2">
                    <button type="button" @click="open = false"
                        class="rounded-lg border dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button type="submit" name="status_dl" value="Ditolak"
                            class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm text-white">
                            Tolak
                        </button>

                        <button type="submit" name="status_dl" value="ACC"
                            class="rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2 text-sm text-white">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button type="submit" name="status_dl" value="Menunggu"
                            class="rounded-lg bg-orange-600 hover:bg-orange-700 px-4 py-2 text-sm text-white">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <div class="space-y-8">
        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Daftar Rencana Kerja Perlu DL / Translok">
                                                                                @endif
                                                                            @endif

                                                                            @can('acceptDL', $penugasan)
                                                                                @if ($penugasan->status_dl === 'ACC')
                                                                                    @if (Auth::user()->active_role === 'Pimpinan')
                                                                                        <form id="del-dl-{{ $penugasan->id_penugasan }}" action="{{ route('kalenderDL.delete', $penugasan->id_penugasan) }}" method="POST" class="w-full">@csrf @method('DELETE')
                                                                                            <button type="button" onclick="SwalHelper.confirmDelete('del-dl-{{ $penugasan->id_penugasan }}', 'Kalender DL milik {{ $penugasan->anggota->nama_pegawai }}')" class="w-full flex items-center justify-center gap-1 px-2.5 py-1 text-[10px] font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-colors shadow-sm" title="Hapus Kalender DL">
                                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                                                HAPUS
                                                                                            </button>
                                                                                        </form>
                                                                                    @endif
                                                                                @elseif (Auth::user()->active_role === 'Pimpinan' && in_array($penugasan->status_dl, ['Menunggu', null]))
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-dl', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri'), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="w-full px-2.5 py-1 text-[10px] font-bold bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors shadow-sm">Verifikasi</button>
                                                                                @endif
                                                                            @endcan
                                                                        </div>
                                                                    @endif

                                                                    {{-- AKSI TRANSLOK --}}
                                                                    @if($penugasan->butuh_translok)
                                                                        <div class="flex flex-wrap items-center gap-1.5 pt-3 border-t border-gray-200 dark:border-gray-700/50 min-h-[38px]">
                                                                            @if ($penugasan->status_translok === 'Ditolak')
                                                                                @if (Auth::user()->active_role === 'Ketua Tim' && Auth::user()->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab)
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-translok', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri'), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="px-2.5 py-1 text-[10px] font-bold bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors shadow-sm w-full flex justify-center">Ajukan Kembali</button>
                                                                                @endif
                                                                                @if (Auth::user()->active_role === 'Pimpinan')
                                                                                    <span class="px-2 py-1 text-[9px] font-medium text-gray-400 dark:text-gray-500 italic">Menunggu Pengajuan</span>
                                                                                @endif
                                                                            @endif

                                                                            @can('acceptTranslok', $penugasan)
                                                                                @if ($penugasan->status_translok === 'ACC')
                                                                                    @if (Auth::user()->active_role === 'Pimpinan')
                                                                                        <form id="del-trl-{{ $penugasan->id_penugasan }}" action="{{ route('kalenderDL.delete', $penugasan->id_penugasan) }}" method="POST" class="w-full">@csrf @method('DELETE')
                                                                                            <button type="button" onclick="SwalHelper.confirmDelete('del-trl-{{ $penugasan->id_penugasan }}', 'Kalender Translok milik {{ $penugasan->anggota->nama_pegawai }}')" class="w-full flex items-center justify-center gap-1 px-2.5 py-1 text-[10px] font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-colors shadow-sm" title="Hapus Kalender Translok">
                                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                                                HAPUS
                                                                                            </button>
                                                                                        </form>
                                                                                    @endif
                                                                                @elseif (Auth::user()->active_role === 'Pimpinan' && in_array($penugasan->status_translok, ['Menunggu', null]))
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-translok', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri'), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="w-full px-2.5 py-1 text-[10px] font-bold bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors shadow-sm">Verifikasi</button>
                                                                                @endif
                                                                            @endcan
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach

                                            @if ($bidang->kegiatans->count() === 0)
                                                <tr>
                                                    <td colspan="8"
                                                        class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                                        Belum ada kegiatan untuk bidang ini
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-center border border-gray-200 dark:border-gray-700 border-t-0">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada kegiatan untuk fungsi ini
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($bidangs->count() === 0)
                    <div
                        class="text-center py-8 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <div
                            class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada fungsi/bidang yang dibuat</p>
                    </div>
                @endif
            </div>
        </x-common.component-card>
    </div>

@endsection
