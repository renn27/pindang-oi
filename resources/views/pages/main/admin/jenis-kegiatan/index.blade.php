@extends('layouts.dashboard')

@section('content')
<x-common.page-breadcrumb pageTitle="{{$title}}" />

    <!-- Bagian Tahun -->
    <div class="flex flex-row items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <!-- Label -->
        <div class="flex items-center h-10">
            <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                Tampilkan Data Tahun
            </label>
        </div>

        <!-- Dropdown -->
        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
            <select
                class="dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:placeholder:text-gray-500 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden"
                :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true">
                <option value="2026" class="text-gray-700 dark:text-gray-300">
                2026
                </option>
            </select>
            <span
                class="pointer-events-none absolute top-1/2 right-3.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </div>

        <!-- Tombol -->
        <button
            type="button"
            class="flex justify-center items-center rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 w-full sm:w-auto h-10 whitespace-nowrap dark:bg-brand-600 dark:hover:bg-brand-700">
            Tampilkan
            </button>
        </div>

        <button class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-jenis-kegiatan',
            })">
            Tambah Jenis Kegiatan
        </button>
    </div>

    <!-- Modal untuk Bidang Kerja -->
    <x-ui.smart-modal id="modal-jenis-kegiatan" class="max-w-xl"
            @open-smart-modal.window="

            if ($event.detail.modalId !== 'modal-jenis-kegiatan') return;

            mode    = $event.detail.mode ?? 'create'
            itemKey  = $event.detail.key ?? null

            let data = $event.detail.data ?? {
                jenis_kegiatan: '',
                butuh_dl_atau_translok: 0,
                kategori: '',
            }

            formData = {
                ...data,
                butuh_dl_atau_translok: Boolean(data.butuh_dl_atau_translok)
            }">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Jenis Kegiatan' : 'Edit Jenis Kegiatan'"></h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan jenis kegiatan baru' : 'Edit jenis kegiatan yang sudah ada'"></p>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-5 dark:bg-gray-900">
            <form :action="mode === 'edit'
                    ? `{{ url('jenis-kegiatan') }}/${itemKey}`
                    : `{{ route('jenis-kegiatan.store') }}`"
                method="POST" class="grid grid-cols-1 gap-y-5">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.jenis_kegiatan" id="jenis_kegiatan" name="jenis_kegiatan"
                        placeholder="Masukkan Jenis Kegiatan Baru"
                        class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <div x-data="{ isOptionSelected: false }" class="mt-2 flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Kategori <span class="text-red-500">*</span>
                    </label>

                    <div class="relative w-full md:w-3/4">
                        <select name="kategori" id="kategori" x-model="formData.kategori"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                            :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true">

                            <option value="" disabled selected class="text-gray-400 dark:text-gray-500">
                                -- Pilih Kategorinya --
                            </option>
                            <option value="Utama" class="text-gray-700 dark:text-gray-300">Utama</option>
                            <option value="Tambahan" class="text-gray-700 dark:text-gray-300">Tambahan</option>
                        </select>

                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- DL / Translok -->
                <div class="mt-2 flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        DL/Translok <span class="text-red-500">*</span>
                    </label>

                    <div class="flex w-full items-center gap-3 md:w-3/4">
                        <!-- Hidden fallback agar nilai 0 tetap terkirim saat unchecked -->
                        <input type="hidden" name="butuh_dl_atau_translok" value=0>

                        <!-- Toggle Switch -->
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox"
                                name="butuh_dl_atau_translok"
                                id="butuh_dl_atau_translok"
                                value="1"
                                :checked="formData.butuh_dl_atau_translok"
                                @change="formData.butuh_dl_atau_translok = $event.target.checked"
                                class="peer sr-only">

                            <!-- Track -->
                            <div class="h-6 w-11 rounded-full border border-gray-300 bg-gray-200
                                after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5
                                after:rounded-full after:border after:border-gray-300 after:bg-white
                                after:transition-all after:content-['']
                                peer-checked:border-brand-500 peer-checked:bg-brand-500
                                peer-checked:after:translate-x-full peer-checked:after:border-white
                                peer-focus:ring-3 peer-focus:ring-brand-500/10
                                dark:border-gray-600 dark:bg-gray-700
                                dark:after:border-gray-600 dark:after:bg-gray-400
                                dark:peer-checked:border-brand-500 dark:peer-checked:bg-brand-500">
                            </div>
                        </label>

                        <!-- Label Status -->
                        <span class="text-sm font-medium"
                            :class="formData.butuh_dl_atau_translok
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-gray-500 dark:text-gray-400'">
                            <template x-if="formData.butuh_dl_atau_translok">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Butuh DL/Translok
                                </span>
                            </template>
                            <template x-if="!formData.butuh_dl_atau_translok">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tidak Butuh
                                </span>
                            </template>
                        </span>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button @click="open = false" type="button"
                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                            <span x-text="mode === 'create' ? 'Simpan Data' : 'Update Data'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.smart-modal>

    <!-- Tabel Utama -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16 dark:text-gray-400">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Jenis Kegiatan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Perlu DL/Translok</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Jumlah Penugasannya</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal Dibuat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @foreach ($jenis_kegiatans as $index => $jenisKegiatan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 text-center dark:text-gray-300">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $jenisKegiatan->jenis_kegiatan }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                            {{ $jenisKegiatan->kategori }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                            {{ $jenisKegiatan->butuh_dl_atau_translok ? 'Ya' : 'Tidak' }}
                        <td class="px-4 py-3 text-sm font-medium text-teal-500 dark:text-gray-300">
                            <a href="{{ route('jenis-kegiatan.detail', $jenisKegiatan->id) }}" class="hover:underline hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                                {{ $jenisKegiatan->penugasans->count() ?? 0 }} Penugasan
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $jenisKegiatan->created_at->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <div class="relative inline-block group">
                                <!-- Button Minimalis -->
                                <button class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-green-400 hover:text-green-600 transition-all duration-200 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-green-500 dark:hover:text-green-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Aksi
                                </button>

                                <!-- Dropdown Simple -->
                                <div class="absolute right-5 top-0 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                    <div class="py-1">
                                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700"
                                            @click="$dispatch('open-smart-modal', {
                                                modalId: 'modal-jenis-kegiatan',
                                                mode: 'edit',
                                                key: '{{ $jenisKegiatan->id }}',
                                                data: {
                                                    jenis_kegiatan: '{{ $jenisKegiatan->jenis_kegiatan }}',
                                                    kategori: '{{ $jenisKegiatan->kategori }}',
                                                    butuh_dl_atau_translok: {{ $jenisKegiatan->butuh_dl_atau_translok ? 'true' : 'false' }},
                                                }
                                            })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>

                                        <form id="delete-rencana-{{ $jenisKegiatan->id }}"
                                            action="{{ route('jenis-kegiatan.delete', $jenisKegiatan->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                onclick="confirmDeleteJenisKegiatan(
                                                    'delete-rencana-{{ $jenisKegiatan->id }}',
                                                    {{ json_encode($jenisKegiatan->jenis_kegiatan) }},
                                                    {{ $jenisKegiatan->penugasans->count() }}
                                                )"
                                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
        function confirmDeleteJenisKegiatan(formId, namaKegiatan, totalPenugasan) {
            if (totalPenugasan > 0) {
                Swal.fire({
                    html: `
                        <div class="text-center p-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 mb-6">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Informasi</h3>
                            <p class="text-justify text-gray-600 dark:text-gray-300 mb-6 font-medium text-sm">
                                Sudah ada <b>${totalPenugasan}</b> penugasan yang dimiliki. Menghapus data ini akan membuat jenis kegiatan kosong di tabel penugasan. Sebaiknya edit dulu data jenis kegiatan di tabel penugasan sebelum menghapus.<br><br>
                            </p>
                            <div class="flex gap-3 justify-center">
                                <button type="button" onclick="Swal.close()"
                                    class="px-5 py-2.5 rounded-lg font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 transition-colors">
                                    Oke
                                </button>
                                <button type="button" onclick="Swal.close(); setTimeout(() => SwalHelper.confirmDelete('${formId}', '${namaKegiatan.replace(/'/g, "\\'")}'), 200);"
                                    class="px-5 py-2.5 rounded-lg font-medium bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white transition-colors">
                                    Tetap Hapus
                                </button>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    customClass: {
                        popup: '!rounded-3xl !border !border-gray-200 !shadow-2xl !bg-white dark:!border-gray-700 dark:!bg-gray-900 !p-0 !max-w-sm',
                        title: '!hidden',
                        htmlContainer: '!p-0 !m-0',
                        container: '!p-5'
                    },
                    backdrop: 'rgba(107, 114, 128, 0.3) dark:rgba(0, 0, 0, 0.5)',
                    buttonsStyling: false
                });
            } else {
                SwalHelper.confirmDelete(formId, namaKegiatan);
            }
        }
    </script>
    @endpush
@endsection
