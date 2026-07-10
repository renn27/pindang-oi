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

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <div class="mt-4">
                            <label for="catatan_pimpinan_translok" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Catatan/Pesan Pimpinan (Opsional)</label>
                            <textarea id="catatan_pimpinan_translok" name="catatan_pimpinan" x-model="formData.catatan_pimpinan" rows="3"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 px-3 py-2 text-sm text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                                placeholder="Masukkan pesan persetujuan atau alasan penolakan..."></textarea>
                        </div>
                    @endif
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

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <div class="mt-4">
                            <label for="catatan_pimpinan_dl" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Catatan/Pesan Pimpinan (Opsional)</label>
                            <textarea id="catatan_pimpinan_dl" name="catatan_pimpinan" x-model="formData.catatan_pimpinan" rows="3"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 px-3 py-2 text-sm text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                                placeholder="Masukkan pesan persetujuan atau alasan penolakan..."></textarea>
                        </div>
                    @endif
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
            <div x-data="{
                searchQuery: '',
                isLoading: false,

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.searchQuery = urlParams.get('search_anggota') || '';
                },

                fetchData() {
                    this.isLoading = true;
                    const url = new URL(window.location.href);
                    if (this.searchQuery) {
                        url.searchParams.set('search_anggota', this.searchQuery);
                    } else {
                        url.searchParams.delete('search_anggota');
                    }

                    window.history.replaceState({}, '', url.toString());

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('rencana-kerja-dl-list-container').innerHTML = html;
                    })
                    .catch(err => console.error(err))
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                clearSearch() {
                    this.searchQuery = '';
                    this.fetchData();
                }
            }">
                <!-- Search input bar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4 border-b border-gray-100 dark:border-gray-800 pb-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Pantau daftar penugasan dinas luar dan translok serta status verifikasinya.
                    </p>
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" 
                            x-model="searchQuery" 
                            x-on:input.debounce.300ms="fetchData()"
                            placeholder="Cari nama anggota..." 
                            class="w-full pl-9 pr-8 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                        />
                        <button x-show="searchQuery !== ''" @click="clearSearch()" type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" style="display: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="rencana-kerja-dl-list-container" :class="{ 'opacity-50 pointer-events-none transition-opacity duration-200': isLoading }">
                    @include('pages.main.pegawai.rencana-kerja.partials.rencana-kerja-dl-list')
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
