@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Kegiatan" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
        <!-- Header Kegiatan -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
            {{$subKegiatan->nama_sub_kegiatan}}
            </h1>

            <!-- Informasi Kegiatan dalam Tabel Format -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 w-32">
                    Sub Kegiatan
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->nama_sub_kegiatan}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Jenis Kegiatan
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->jenis_kegiatan}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Tanggal Mulai
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->tanggal_mulai->format('d M Y')}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Tanggal Berakhir
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->tanggal_selesai->format('d M Y')}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Satuan Target
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->satuan_target}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Status
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->status}}
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Section Progres -->
        <div class="mb-8 max-w-full overflow-hidden">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Progres</h2>
            <div class="flex justify-start mb-6">
                <button class="flex items-center gap-2 rounded-full border border-gray-300
                        bg-white px-4 py-3 text-sm font-medium text-gray-700
                        shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                        dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                        @click="$dispatch('open-smart-modal', {
                            modalId: 'modal-penugasan-anggota',
                            data: {
                                id_sub_kegiatan: '{{ $subKegiatan->id_sub_kegiatan }}',
                                nama_sub_kegiatan: '{{ $subKegiatan->nama_sub_kegiatan }}'
                            }
                        })">
                    <!-- icon -->
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                        fill="" />
                    </svg>
                    Tambah Anggota
                </button>
            </div>

            <x-ui.smart-modal id="modal-penugasan-anggota" class="max-w-xl"
                @open-smart-modal.window="
                    if ($event.detail.modalId !== 'modal-penugasan-anggota') return;

                    mode = $event.detail.mode ?? 'create';
                    itemKey = $event.detail.key ?? null;
                    // Ambil data dari dispatch
                    formData = $event.detail.data ?? {
                        id_sub_kegiatan: '',
                        nama_sub_kegiatan: '',
                        id_anggota: '',
                        nama_anggota: '',
                        target: '',
                        tanggal_mulai: '',
                        tanggal_selesai: '',
                        status: ''
                    }">
                <div
                    class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                            rounded-3xl bg-white dark:bg-gray-900">

                    <!-- HEADER (FIXED) -->
                    <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="mode === 'create' ? 'Tambah Anggota' : 'Edit Data Anggota'"></h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Tambahkan penugasan kepada anggota' : 'Edit anggota yang sudah ditugaskan'"></p>
                    </div>

                    <!-- BODY (SCROLL DI SINI) -->
                    <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                        <form :action="mode === 'edit'
                                ? `/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${itemKey}`
                                : `/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan`"
                            method="POST"
                            class="grid grid-cols-1 gap-y-5">
                            @csrf
                            <template x-if="mode === 'edit'">
                                @method('PUT')
                            </template>

                            <!-- Nama Sub Kegiatan (readonly tampilan) -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Sub Kegiatan
                                </label>

                                <input
                                    type="text"
                                    :value="formData.nama_sub_kegiatan"
                                    disabled
                                    class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                            </div>

                            {{-- Nama Ketua / Penanggung Jawab --}}
                            <div x-data="{
                                open: false,
                                search: '',
                                selectedId: '',
                                highlightedIndex: -1,
                                pegawais: @js($pegawais),

                                filtered() {
                                    if(this.search.length === 0) return [];
                                    return this.pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase()));
                                },

                                selectPegawai(p) {
                                    this.search = p.nama_pegawai;
                                    this.selectedId = p.id_pegawai;
                                    this.open = false;
                                    this.highlightedIndex = -1;
                                },

                                highlightNext() { if(this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                                highlightPrev() { if(this.highlightedIndex > 0) this.highlightedIndex--; },
                                selectHighlighted() { if(this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                                }">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama Anggota
                                    </label>
                                    <!-- Input search -->
                                    <input type="text" x-model="search" @focus="open = !!search" @input="open = search.length > 0; selectedId = ''"
                                    @keydown.arrow-down.prevent="highlightedIndex++"
                                    @keydown.arrow-up.prevent="highlightedIndex--"
                                    @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }"
                                    placeholder="Ketik untuk cari nama" class="h-11 w-full rounded-lg border px-4 py-2 text-sm">

                                <!-- Hidden input -->
                                <input type="hidden" name="id_anggota" :value="selectedId" required>

                                <!-- Dropdown -->
                                <div x-show="open" x-transition class="absolute z-50 mt-1 w-full rounded-lg border bg-white max-h-60 overflow-y-auto">
                                    <template x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))" :key="pegawai.id_pegawai">
                                        <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                                            :class="{'bg-blue-100': highlightedIndex===index, 'hover:bg-gray-100': highlightedIndex!==index}" class="cursor-pointer px-4 py-2 text-sm" x-text="pegawai.nama_pegawai"></div>
                                    </template>
                                    <template x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                                        <div class="px-4 py-2 text-sm text-gray-500">Data tidak ditemukan</div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Target
                                </label>
                                <input type="number" name="target" placeholder="Masukkan Target"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Mulai
                                </label>
                                <x-form.date-picker
                                    id="tanggal_mulai"
                                    name="tanggal_mulai"
                                    placeholder="Date Picker"
                                    defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Akhir
                                </label>
                                <x-form.date-picker
                                    id="tanggal_akhir"
                                    name="tanggal_selesai"
                                    placeholder="Date Picker"
                                    defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Status
                                </label>
                                <select
                                    name="status"
                                    x-model="formData.status"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Belum Dikirim" :selected="formData.status === 'Belum Dikirim'">Belum Dikirim</option>
                                    <option value="Sudah Dikirim" :selected="formData.status === 'Sudah Dikirim'">Sudah Dikirim</option>
                                    <option value="Masih Revisi" :selected="formData.status === 'Masih Revisi'">Masih Revisi</option>
                                    <option value="Sudah Diterima" :selected="formData.status === 'Sudah Diterima'">Sudah Diterima</option>
                                </select>
                            </div>
                            <!-- FOOTER -->
                            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                                <div class="flex justify-end gap-3">
                                    <button type="button"
                                            @click="open = false"
                                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                                text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        Batal
                                    </button>

                                    <button type="submit"
                                        class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </x-ui.smart-modal>

            <!-- Modal Tambah Pengiriman -->
            <x-ui.smart-modal id="modal-pengiriman-anggota" class="max-w-xl"
                @open-smart-modal.window="
                    if ($event.detail.modalId !== 'modal-pengiriman-anggota') return;

                    mode = $event.detail.mode ?? 'create';
                    itemKey = $event.detail.key ?? null;
                    // Ambil data dari dispatch
                    formData = $event.detail.data ?? {
                        id_sub_kegiatan: '',
                        id_penugasan: '',
                        nama_anggota: ''
                        tanggal_pengiriman: '',
                        jumlah_dikirim: '',
                        media_dikirim: '',
                        bukti_dukung: ''
                    }">
                <div
                    class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                            rounded-3xl bg-white dark:bg-gray-900">

                    <!-- HEADER (FIXED) -->
                    <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Buat Pengiriman
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Kirimkan hasil kerja disini
                        </p>
                    </div>

                    <!-- BODY (SCROLL DI SINI) -->
                    <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                        <form :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/pengiriman`"
                            method="POST" class="grid grid-cols-1 gap-y-5">
                            @csrf
                            <!-- Id Penugasan (readonly tampilan) -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Id Penugasan
                                </label>

                                <input
                                    type="text"
                                    :value="formData.id_penugasan"
                                    disabled
                                    class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                            </div>

                            <!-- Nama Anggota (readonly tampilan) -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama Anggota
                                </label>

                                <input
                                    type="text"
                                    :value="formData.nama_anggota"
                                    disabled
                                    class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Pengiriman
                                </label>
                                <x-form.date-picker
                                    id="tanggal_pengiriman"
                                    name="tanggal_pengiriman"
                                    placeholder="Date Picker"
                                    defaultDate="{{ now()->format('Y-m-d') }}"
                                    readonly="true" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Jumlah Dikirim
                                </label>
                                <input type="text" name="jumlah_dikirim" placeholder="Masukkan jumlah pengiriman"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Media Pengiriman
                                </label>
                                <input type="text" name="media_pengiriman" placeholder="Masukkan jenis media pengiriman"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Bukti Dukung
                                </label>
                                <input type="text" name="bukti_dukung" placeholder="Masukkan link bukti dukung pengiriman"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>
                            <!-- FOOTER -->
                            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                                <div class="flex justify-end gap-3">
                                    <button type="button"
                                            @click="open = false"
                                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                                text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        Batal
                                    </button>

                                    <button type="submit"
                                        class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </x-ui.smart-modal>

            <!-- Modal Histori Pengiriman Anggota -->
            <x-ui.smart-modal id="modal-histori-pengiriman" class="max-w-xl"
                @open-smart-modal.window="
                    if ($event.detail.modalId !== 'modal-histori-pengiriman') return;

                    formData = $event.detail.data ?? {
                        nama_anggota: '',
                        id_penugasan: '',
                        historiData: []
                    };">
                <div
                    class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
                            rounded-3xl bg-white dark:bg-gray-900">

                    <!-- HEADER -->
                    <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90"
                            x-text="formData.nama_anggota">
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            ID Penugasan:
                            <span class="font-medium" x-text="formData.id_penugasan"></span>
                        </p>
                    </div>

                    <!-- BODY -->
                    <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar space-y-4">

                        <!-- Jika belum ada pengiriman -->
                        <template x-if="formData.historiData.length === 0">
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm
                                        text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Belum ada histori pengiriman
                            </div>
                        </template>

                        <!-- Card Histori Pengiriman -->
                        <template x-for="(item, index) in formData.historiData" :key="index">
                            <div
                                class="rounded-2xl border border-gray-200 p-5 shadow-sm
                                        dark:border-gray-700 dark:bg-gray-800">

                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                            Pengiriman ke-<span x-text="index + 1"></span>
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                        x-text="item.tanggal_pengiriman">
                                        </p>
                                    </div>

                                    <!-- Status -->
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-medium"
                                        :class="item.status === 'Diterima'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'"
                                        x-text="item.status">
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Jumlah Dikirim</p>
                                        <p class="font-medium text-gray-800 dark:text-white/90"
                                        x-text="item.jumlah_dikirim">
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400">Media Pengiriman</p>
                                        <p class="font-medium text-gray-800 dark:text-white/90"
                                        x-text="item.media_pengiriman">
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Bukti Dukung</p>
                                    <a :href="item.bukti_dukung" target="_blank"
                                    class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                                        Lihat Bukti Dukung
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- FOOTER -->
                    <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                        <div class="flex justify-end">
                            <button type="button"
                                    @click="open = false"
                                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                        text-gray-700 hover:bg-gray-50
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </x-ui.smart-modal>

            <!-- Modal Tambah Penerimaan -->
            <x-ui.smart-modal id="modal-penerimaan-anggota" class="max-w-xl"
                @open-smart-modal.window="
                    if ($event.detail.modalId !== 'modal-penerimaan-anggota') return;

                    mode = $event.detail.mode ?? 'create';
                    itemKey = $event.detail.key ?? null;
                    // Ambil data dari dispatch
                    formData = $event.detail.data ?? {
                        id_sub_kegiatan: '',
                        id_penugasan: '',
                        id_pengiriman: '',
                        id_penerima: '',
                        nama_penerima: ''
                        tanggal_penerimaan: '',
                        jumlah_diterima: '',
                        status: '',
                        catatan: ''
                    }">
                <div
                    class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                            rounded-3xl bg-white dark:bg-gray-900">

                    <!-- HEADER (FIXED) -->
                    <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Lakukan Penerimaan
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Terima dan Berikan Penilaian Kerja
                        </p>
                    </div>
                    <div class="text-center">
                        <h6 class="text-sm font-semibold text-gray-600 dark:text-white/90"
                            x-text="formData.nama_anggota">
                        </h6>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            ID Penugasan:
                            <span class="font-medium" x-text="formData.id_penugasan"></span>
                        </p>
                    </div>

                    <!-- BODY (SCROLL DI SINI) -->
                    <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                        <form :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/pengirimans/${formData.id_pengiriman}/penerimaan`"
                            method="POST" class="grid grid-cols-1 gap-y-5">
                            @csrf
                            <!-- Id Pengiriman (readonly tampilan) -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Id Pengiriman
                                </label>

                                <input
                                    type="text"
                                    :value="formData.id_pengiriman"
                                    disabled
                                    class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tanggal Penerimaan
                                </label>
                                <x-form.date-picker
                                    id="tanggal_penerimaan"
                                    name="tanggal_penerimaan"
                                    placeholder="Date Picker"
                                    defaultDate="{{ now()->format('Y-m-d') }}"
                                    readonly="true" />
                                </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Jumlah Diterima
                                </label>
                                <input type="text" name="jumlah_diterima" placeholder="Masukkan jumlah penerimaan"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>
                            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Status
                                </label>
                                <select
                                    name="status"
                                    x-model="formData.status"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                        dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg
                                        border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                                        placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                        dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                                        dark:placeholder:text-white/30"
                                    :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                                    @change="isOptionSelected = true">
                                    <!-- Placeholder -->
                                    <option value="" disabled selected class="text-gray-400 dark:bg-gray-900">
                                        -- Pilih Status --
                                    </option>

                                    <option value="Diterima" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                        Diterima
                                    </option>

                                    <option value="Revisi" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                        Revisi
                                    </option>
                                </select>

                                <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2
                                        text-gray-700 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Catatan
                                </label>
                                <input type="text" name="catatan" placeholder="Masukkan catatan"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>
                            <!-- FOOTER -->
                            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                                <div class="flex justify-end gap-3">
                                    <button type="button"
                                            @click="open = false"
                                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                                text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                        Batal
                                    </button>

                                    <button type="submit"
                                        class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </x-ui.smart-modal>

            <!-- Tabel Detail Pengiriman - VERSI BERSIH -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <div class="grid grid-cols-1">
                    <div class="col-span-1 overflow-x-auto">
                        <table class="max-w-[1400px] w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    No.
                                    </th>
                                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Nama
                                    </th>
                                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Target
                                    </th>
                                    <th colspan="3" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Pengiriman
                                    </th>
                                    <th colspan="4" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Penerimaan
                                    </th>
                                    <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Aksi
                                    </th>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Detail
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    RR (%)
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Ketepatan Waktu
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Detail
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    RR (%)
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Ketepatan Waktu
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    Bukti Dukung
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse ($subKegiatan->penugasans as $index => $penugasan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                            {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                            {{ $penugasan->target ?? '-' }}
                                        </td>

                                        {{-- PENGIRIMAN --}}
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                                                    {{ $penugasan->latestPengiriman->tanggal_pengiriman->translatedFormat('D, d M Y') ?? 'belum dikirim' }}
                                                    Jumlah : {{ $penugasan->latestPengiriman->jumlah_dikirim ?? '-' }}
                                                    Dikirim melalui {{ $penugasan->latestPengiriman->media_pengiriman ?? '-' }}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                            {{ $penugasan->rr_kirim ? $penugasan->rr_kirim.'%' : '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                            <div class="flex justify-center">
                                                @for($i = 0; $i < ($penugasan->rating_kirim ?? 0); $i++)
                                                    <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </td>

                                        {{-- PENERIMAAN --}}
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                            <div>
                                                <div class="flex items-center">
                                                    <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                                                    <span>
                                                        {{ optional($penugasan->tanggal_terima)->format('D, d M Y') ?? '-' }}
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                                                    Jumlah: {{ $penugasan->jumlah_terima ?? '-' }}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                            {{ $penugasan->rr_terima ? $penugasan->rr_terima.'%' : '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                            <div class="flex justify-center">
                                                @for($i = 0; $i < ($penugasan->rating_terima ?? 0); $i++)
                                                    <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                            <a href="{{ $penugasan->bukti_dukung ?: 'https://www.youtube.com/' }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="{{ $penugasan->bukti_dukung ? 'Buka bukti dukung' : 'Belum ada bukti dukung' }}"
                                                class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                                {{ $penugasan->bukti_dukung ? 'Lihat Bukti' : 'Belum Ada' }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center border-r border-gray-200 dark:border-gray-800">
                                            <!-- Container untuk tombol saja -->
                                            <div class="relative inline-block" x-data="{
                                                showDropdown: false,
                                                dropdownPosition: { x: 0, y: 0 },
                                                calculatePosition(button) {
                                                    const rect = button.getBoundingClientRect();
                                                    const dropdownWidth = 192; // w-48 = 192px
                                                    const dropdownHeight = 132; // perkiraan tinggi 3 tombol

                                                    // Coba muncul di bawah tombol
                                                    let left = rect.left + (rect.width / 2) - (dropdownWidth / 2);
                                                    let top = rect.bottom + 8;

                                                    // Cek jika akan keluar dari viewport kanan
                                                    if (left + dropdownWidth > window.innerWidth - 10) {
                                                        left = window.innerWidth - dropdownWidth - 10;
                                                    }

                                                    // Cek jika akan keluar dari viewport kiri
                                                    if (left < 10) {
                                                        left = 10;
                                                    }

                                                    // Cek jika akan keluar dari viewport bawah
                                                    if (top + dropdownHeight > window.innerHeight - 10) {
                                                        // Jika tidak muat di bawah, muncul di atas
                                                        top = rect.top - dropdownHeight - 8;
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
                                                }" x-on:mouseleave="closeDropdown()">

                                                <!-- Tombol utama dengan hover -->
                                                <button
                                                    x-on:mouseenter="openDropdown($event)"
                                                    class="inline-flex items-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                                    </svg>
                                                    Aksi
                                                </button>

                                                <!-- Dropdown menu FIXED POSITION -->
                                                <div x-show="showDropdown"
                                                    x-transition:enter="transition ease-out duration-150"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-100"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="fixed z-[9999] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 min-w-[192px]"
                                                    :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                                    x-on:mouseenter="showDropdown = true"
                                                    x-on:mouseleave="closeDropdown()">

                                                    <!-- Tombol Buat Pengiriman -->
                                                    <button class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20 dark:hover:text-blue-300 flex items-center gap-2 whitespace-nowrap border-b border-gray-100 dark:border-gray-700"
                                                            @click="$dispatch('open-smart-modal', {
                                                                modalId: 'modal-pengiriman-anggota',
                                                                data: {
                                                                    id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                                    id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                    nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                }
                                                            })">
                                                        <!-- icon -->
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Buat Pengiriman
                                                    </button>

                                                    <!-- Tombol Tampilkan Histori Pengiriman -->
                                                    <button
                                                        class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300
                                                            hover:bg-blue-50 hover:text-blue-600
                                                            dark:hover:bg-blue-900/20 dark:hover:text-blue-300
                                                            flex items-center gap-2 whitespace-nowrap
                                                            border-b border-gray-100 dark:border-gray-700"
                                                        @click="$dispatch('open-smart-modal', {
                                                            modalId: 'modal-histori-pengiriman',
                                                            data: {
                                                                id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                historiData: @js($penugasan->pengirimans->map(fn($p) => [
                                                                    'tanggal_pengiriman' => $p->tanggal_pengiriman->translatedFormat('d F Y'),
                                                                    'jumlah_dikirim' => $p->jumlah_dikirim,
                                                                    'media_pengiriman' => $p->media_pengiriman,
                                                                    'bukti_dukung' => $p->bukti_dukung,
                                                                    'status' => $p->penerimaan?->status ?? 'Belum Diproses', // Diterima / Revisi
                                                                ]))
                                                            }
                                                        })">
                                                        <!-- Icon -->
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                                                        </svg>

                                                        Tampilkan Histori Pengiriman
                                                    </button>


                                                    <!-- Tombol Buat Penerimaan -->
                                                    <button class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20 dark:hover:text-blue-300 flex items-center gap-2 whitespace-nowrap border-b border-gray-100 dark:border-gray-700"
                                                            @click="$dispatch('open-smart-modal', {
                                                                modalId: 'modal-penerimaan-anggota',
                                                                data: {
                                                                    id_sub_kegiatan: '{{ $penugasan->subKegiatan->id_sub_kegiatan }}',
                                                                    id_penugasan: '{{ $penugasan->id_penugasan }}',
                                                                    id_pengiriman: '{{ $penugasan->latestPengiriman->id_pengiriman }}',
                                                                    nama_anggota: '{{ $penugasan->anggota->nama_pegawai }}',
                                                                }
                                                            })">
                                                        <!-- icon -->
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Buat Penerimaan
                                                    </button>

                                                    <!-- Tombol Jadikan CKP -->
                                                    <button
                                                        @click="closeDropdown()"
                                                        class="w-full rounded-lg text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:text-green-300 flex items-center gap-2 whitespace-nowrap">
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
