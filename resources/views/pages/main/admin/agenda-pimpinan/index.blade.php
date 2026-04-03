@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <!-- Bagian Filter Tahun (opsional, bisa diaktifkan jika diperlukan) -->
    <div
        class="flex flex-row items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <!-- Label -->
            <div class="flex items-center h-10">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Tampilkan Data Tahun
                </label>
            </div>

            <!-- Dropdown -->
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select id="tahunFilter"
                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:placeholder:text-gray-500 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden"
                    :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true">
                    <option value="2026" class="text-gray-700 dark:text-gray-300">2026</option>
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

            <!-- Tombol Tampilkan -->
            <button id="filterButton" type="button"
                class="flex justify-center items-center rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 w-full sm:w-auto h-10 whitespace-nowrap dark:bg-brand-600 dark:hover:bg-brand-700">
                Tampilkan
            </button>
        </div>

        <!-- Tombol Tambah Agenda -->
        <button
            class="gap-2 rounded-full border border-gray-300
        bg-white px-4 py-3 text-sm font-medium text-gray-700
        shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
            @click="$dispatch('open-smart-modal', {
            modalId: 'modal-agenda',
            mode: 'create'
        })">
            Tambah Agenda
        </button>
    </div>

    <!-- MODAL AGENDA PIMPINAN -->
    <x-ui.smart-modal id="modal-agenda" class="max-w-2xl"
        @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-agenda') return;

        mode = $event.detail.mode ?? 'create';
        itemKey = $event.detail.key ?? null;

        let baseData = $event.detail.data || {};
        formData = {
            nama_kegiatan: '',
            tanggal_mulai: '',
            tanggal_selesai: '',
            id_rencana_jpt: '',
            id_indikator_jpt: '',
            link_bukti: '',
            status: 'Belum Selesai',
            ikiOptions: [],
            _pendingIndikatorId: '',
            ...baseData
        };
    ">

        <form
            :action="mode === 'edit'
                ? `/agenda-pimpinan/${itemKey}`
                : `{{ route('agenda.store') }}`"
            method="POST" class="grid grid-cols-1 gap-y-5">
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

                <!-- HEADER -->
                <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                        x-text="mode === 'create' ? 'Tambah Agenda Pimpinan' : 'Edit Agenda Pimpinan'"></h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        x-text="mode === 'create' ? 'Masukkan agenda pimpinan baru' : 'Edit agenda pimpinan yang sudah ada'">
                    </p>
                </div>

                <!-- BODY (SCROLL) -->
                <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                    {{-- VALIDATION BANNER --}}
                    <div id="validationBannerAgenda"
                        class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3 mb-4">
                        <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                            ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                        </p>
                        <ul id="validationListAgenda" class="list-disc pl-5 space-y-1"></ul>
                    </div>

                    <!-- Nama Kegiatan -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nama Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="formData.nama_kegiatan" name="nama_kegiatan" id="nama_kegiatan"
                            placeholder="Masukkan Nama Kegiatan"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                        <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="nama_kegiatan">Nama kegiatan wajib diisi</p>
                    </div>

                    <!-- Waktu Mulai -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Waktu Mulai <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker id="tanggal_mulai" x-model="formData.tanggal_mulai" name="tanggal_mulai"
                            placeholder="Pilih Tanggal" />
                        <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="tanggal_mulai">Waktu mulai wajib dipilih</p>
                    </div>

                    <!-- Waktu Selesai -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Waktu Selesai <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker id="tanggal_selesai" name="tanggal_selesai" x-model="formData.tanggal_selesai"
                            placeholder="Pilih Tanggal" />
                        <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="tanggal_selesai">Waktu selesai wajib dipilih</p>
                    </div>

                    <!-- Rencana JPT -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rencana JPT <span class="text-red-500">*</span>
                        </label>
                        <select id="id_rencana_jpt" name="id_rencana_jpt" x-model="formData.id_rencana_jpt"
                            @change="
                                formData.id_indikator_jpt = '';
                                formData._pendingIndikatorId = '';
                                loadIkiByRkForAgenda(formData.id_rencana_jpt, formData)
                            "
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <option value="" class="dark:text-gray-400">-- Pilih RK JPT --</option>
                            @foreach ($rencanaJpts as $rk)
                                <option value="{{ $rk->id }}" class="dark:text-gray-300">
                                    {{ $rk->nama_rencana_jpt }}
                                </option>
                            @endforeach
                        </select>
                        <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="id_rencana_jpt">Rencana JPT wajib dipilih</p>
                    </div>

                    <!-- Indikator JPT -->
<div class="mb-4">
    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
        Indikator JPT <span class="text-red-500">*</span>
    </label>
    <input type="hidden" name="id_indikator_jpt" id="id_indikator_jpt_hidden">
    <select id="id_indikator_jpt"
        @change="document.getElementById('id_indikator_jpt_hidden').value = $event.target.value"
        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
        <option value="">-- Harap pilih RK JPT dulu --</option>
    </select>
    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden"
        data-for="id_indikator_jpt">Indikator JPT wajib dipilih</p>
</div>

                    <!-- Link Bukti -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Link Bukti
                        </label>
                        <input type="url" x-model="formData.link_bukti" name="link_bukti" id="link_bukti"
                            placeholder="https://example.com/bukti"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                        <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="link_bukti">Link bukti harus berupa URL yang valid</p>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" x-model="formData.status"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <option value="Selesai" class="dark:text-gray-300">Selesai</option>
                            <option value="Belum Selesai" class="dark:text-gray-300">Belum Selesai</option>
                        </select>
                        <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="status">Status wajib dipilih</p>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button @click="open = false" type="button"
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button x-show="mode === 'create'" id="saveAgendaButton" type="button"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                            Simpan Agenda
                        </button>
                        <button x-show="mode !== 'create'" type="submit"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                            Ubah Data Agenda
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </x-ui.smart-modal>

    <!-- TABEL UTAMA AGENDA PIMPINAN -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-x-auto">
        <table class="min-w-[1200px] lg:min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12 dark:text-gray-400">
                        No.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48 dark:text-gray-400">
                        Nama Kegiatan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28 dark:text-gray-400">
                        Waktu
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                        RK JPT
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                        IKI JPT
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32 dark:text-gray-400">
                        Bukti Dukung
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20 dark:text-gray-400">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="agendaTableBody" class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @foreach ($agenda as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 agenda-row"
                        data-tahun="{{ date('Y', strtotime($item->tanggal_mulai)) }}">
                        <td
                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-gray-300">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            <div class="max-w-xs break-words">{{ $item->nama_kegiatan }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                            <div>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">sd</div>
                            <div>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="max-w-md">
                                <div title="{{ $item->rencanaJpt->nama_rencana_jpt ?? '-' }}">
                                    {{ $item->rencanaJpt->nama_rencana_jpt ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="max-w-md">
                                <div title="{{ $item->indikatorJpt->nama_indikator_jpt ?? '-' }}">
                                    {{ $item->indikatorJpt->nama_indikator_jpt ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $item->status == 'Selesai'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if ($item->link_bukti)
                                <a href="{{ $item->link_bukti }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 border border-blue-300 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-all duration-200 dark:bg-blue-900/30 dark:border-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                    title="{{ $item->link_bukti }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                    Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="relative inline-block group">
                                <!-- Button Aksi Minimalis -->
                                <button
                                    class="inline-flex items-center gap-1 rounded-lg bg-white border border-gray-300 px-2.5 py-1 text-xs font-medium text-gray-700 hover:border-green-400 hover:text-green-600 transition-all duration-200 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-green-500 dark:hover:text-green-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Aksi
                                </button>

                                <!-- Dropdown -->
                                <div
                                    class="absolute right-0 top-full mt-1 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                    <div class="py-1">
                                        <button
                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700"
                                            @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-agenda',
                                            mode: 'edit',
                                            key: '{{ $item->id }}',
                                            data: {
                                                nama_kegiatan: '{{ addslashes($item->nama_kegiatan) }}',
                                                tanggal_mulai: '{{ $item->tanggal_mulai }}',
                                                tanggal_selesai: '{{ $item->tanggal_selesai }}',
                                                id_rencana_jpt: '{{ $item->id_rencana_jpt }}',
                                                id_indikator_jpt: '{{ $item->id_indikator_jpt }}',
                                                link_bukti: '{{ $item->link_bukti }}',
                                                status: '{{ $item->status }}'
                                            }
                                        })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>

                                        <form id="delete-agenda-{{ $item->id }}"
                                            action="{{ route('agenda.delete', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDeleteAgenda('delete-agenda-{{ $item->id }}', '{{ addslashes($item->nama_kegiatan) }}')"
                                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
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

    <script>
        // =============================================
        // FUNGSI LOAD IKI BERDASARKAN RK
        // =============================================
        async function loadIkiByRkForAgenda(rkId, formData) {
    const selectEl = document.getElementById('id_indikator_jpt');
    const hiddenEl = document.getElementById('id_indikator_jpt_hidden');

    if (!rkId) {
        if (selectEl) {
            selectEl.innerHTML = '<option value="">-- Harap pilih RK JPT dulu --</option>';
        }
        if (hiddenEl) hiddenEl.value = '';
        return;
    }

    // Tampilkan loading sementara
    if (selectEl) {
        selectEl.innerHTML = '<option value="">Memuat...</option>';
        selectEl.disabled = true;
    }

    try {
        const response = await fetch(`/rencana-indikator-jpt/${rkId}/indikator`);
        const data = await response.json();

        if (!selectEl) return;

        // Build options langsung ke DOM
        const pending = formData._pendingIndikatorId ? String(formData._pendingIndikatorId) : '';
        formData._pendingIndikatorId = '';

        let html = '<option value="">-- Pilih IKI JPT --</option>';
        data.forEach(iki => {
            const selected = String(iki.id) === pending ? 'selected' : '';
            html += `<option value="${iki.id}" ${selected}>${iki.nama_indikator_jpt}</option>`;
        });

        selectEl.innerHTML = html;
        selectEl.disabled = false;

        // Sync ke hidden input
        if (hiddenEl) hiddenEl.value = selectEl.value;

    } catch (error) {
        console.error('Error loading IKI:', error);
        if (selectEl) {
            selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
            selectEl.disabled = false;
        }
    }
}

        // =============================================
        // VALIDASI FRONTEND
        // =============================================
        function clearValidationAgenda() {
            document.querySelectorAll('.input-invalid').forEach(el => {
                el.classList.remove(
                    'input-invalid',
                    'border-red-500', 'dark:border-red-500',
                    'bg-red-50', 'dark:bg-red-500/10'
                );
            });

            document.querySelectorAll('.field-error-msg').forEach(el => {
                el.classList.add('hidden');
            });

            const banner = document.getElementById('validationBannerAgenda');
            if (banner) banner.classList.add('hidden');
        }

        function markInvalidAgenda(el) {
            if (!el) return;
            el.classList.add(
                'input-invalid',
                'border-red-500', 'dark:border-red-500',
                'bg-red-50', 'dark:bg-red-500/10'
            );
        }

        function validateFormAgenda() {
            clearValidationAgenda();
            const errors = [];

            function addError(message, focusEl, inputEl) {
                errors.push({ message, focusEl });
                if (inputEl) markInvalidAgenda(inputEl);
                const errorMsg = inputEl?.closest('.mb-4')?.querySelector('.field-error-msg');
                if (errorMsg) errorMsg.classList.remove('hidden');
            }

            // Nama Kegiatan
            const namaKegiatan = document.getElementById('nama_kegiatan');
            if (!namaKegiatan?.value?.trim()) {
                addError('Nama kegiatan wajib diisi', namaKegiatan, namaKegiatan);
            }

            // Waktu Mulai
            const tglMulai = document.getElementById('tanggal_mulai');
            if (!tglMulai?.value) {
                addError('Waktu mulai wajib diisi', tglMulai, tglMulai);
            }

            // Waktu Selesai
            const tglSelesai = document.getElementById('tanggal_selesai');
            if (!tglSelesai?.value) {
                addError('Waktu selesai wajib diisi', tglSelesai, tglSelesai);
            } else if (tglMulai?.value && tglSelesai.value < tglMulai.value) {
                addError('Waktu selesai harus setelah atau sama dengan waktu mulai', tglSelesai, tglSelesai);
            }

            // RK JPT
            const rkJpt = document.getElementById('id_rencana_jpt');
            if (!rkJpt?.value) {
                addError('Rencana JPT wajib dipilih', rkJpt, rkJpt);
            }

            // IKI JPT
            const ikiJpt = document.getElementById('id_indikator_jpt');
            if (!ikiJpt?.value) {
                addError('Indikator JPT wajib dipilih', ikiJpt, ikiJpt);
            }

            // Link Bukti
            const linkBukti = document.getElementById('link_bukti');
            if (linkBukti?.value?.trim()) {
                const urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
                if (!urlPattern.test(linkBukti.value.trim())) {
                    addError('Link bukti harus berupa URL yang valid', linkBukti, linkBukti);
                }
            }

            // Status
            const status = document.querySelector('select[name="status"]');
            if (!status?.value) {
                addError('Status wajib dipilih', status, status);
            }

            return errors;
        }

        function showValidationAgendaBanner(errors) {
            const banner = document.getElementById('validationBannerAgenda');
            const list = document.getElementById('validationListAgenda');
            if (!banner || !list) return;

            list.innerHTML = '';
            errors.forEach(err => {
                const li = document.createElement('li');
                li.textContent = err.message;
                li.className = 'text-xs text-red-600 dark:text-red-400 cursor-pointer hover:underline';
                if (err.focusEl) {
                    li.onclick = () => {
                        err.focusEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => err.focusEl.focus(), 300);
                    };
                }
                list.appendChild(li);
            });

            banner.classList.remove('hidden');
            banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function saveAgenda(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const errors = validateFormAgenda();
            if (errors.length > 0) {
                showValidationAgendaBanner(errors);
                return;
            }

            const form = document.querySelector('#modal-agenda form');
            if (form) form.submit();
        }

        // =============================================
        // KONFIRMASI HAPUS
        // =============================================
        function confirmDeleteAgenda(formId, namaKegiatan) {
            SwalHelper.confirmDelete(formId, namaKegiatan);
        }

        // =============================================
        // FILTER TAHUN
        // =============================================
        document.addEventListener('DOMContentLoaded', function() {
    const saveButton = document.getElementById('saveAgendaButton');
    if (saveButton) {
        saveButton.addEventListener('click', saveAgenda);
    }

    const filterButton = document.getElementById('filterButton');
    const tahunSelect = document.getElementById('tahunFilter');
    const rows = document.querySelectorAll('.agenda-row');
    let currentTahun = 'all';

    function filterTable() {
        if (currentTahun === 'all') {
            rows.forEach(row => row.style.display = '');
        } else {
            rows.forEach(row => {
                const rowTahun = row.getAttribute('data-tahun');
                row.style.display = rowTahun === currentTahun ? '' : 'none';
            });
        }
    }

    if (filterButton && tahunSelect) {
        filterButton.addEventListener('click', function() {
            currentTahun = tahunSelect.value;
            filterTable();
        });
    }

    // Listen event yang sama dengan yang di-dispatch tombol Edit
    // Event ini terpanggil SEBELUM Alpine handle, jadi kita simpan data dulu
    window.addEventListener('open-smart-modal', function(e) {
        const detail = e.detail;
        if (detail.modalId !== 'modal-agenda' || detail.mode !== 'edit') return;
        if (!detail.data?.id_rencana_jpt) return;

        // Simpan pending data
        window._agendaEditPending = {
            rkId: String(detail.data.id_rencana_jpt),
            indikatorId: String(detail.data.id_indikator_jpt || '')
        };

        // Tunggu Alpine selesai toggle modal (x-show jalan setelah event handler selesai)
        // Kita poll sampai select IKI berisi "Memuat..." yang artinya loadIki sudah terpanggil
        // ATAU langsung set timeout yang cukup untuk Alpine render
        const checkAndLoad = (attempt) => {
            const selectEl = document.getElementById('id_indikator_jpt');
            if (!selectEl) {
                if (attempt < 20) requestAnimationFrame(() => checkAndLoad(attempt + 1));
                return;
            }

            // Cek apakah modal sudah visible
            const modalEl = document.getElementById('modal-agenda');
            const isVisible = modalEl && getComputedStyle(modalEl).display !== 'none';

            if (isVisible && window._agendaEditPending) {
                const { rkId, indikatorId } = window._agendaEditPending;
                window._agendaEditPending = null;
                loadIkiByRkForAgenda(rkId, { _pendingIndikatorId: indikatorId });
            } else if (attempt < 30) {
                requestAnimationFrame(() => checkAndLoad(attempt + 1));
            }
        };

        requestAnimationFrame(() => checkAndLoad(0));
    });
});
    </script>

    <style>
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endsection