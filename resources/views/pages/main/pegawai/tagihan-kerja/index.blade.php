@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <!-- Bagian Tahun -->
    <div
        class="flex flex-col sm:flex-row items-start sm:items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 gap-4 sm:gap-0 dark:border-gray-800 dark:bg-gray-900">
        <!-- Bagian Kiri: Filter Tahun -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto">
            <!-- Label -->
            <div class="flex items-center h-10">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Tampilkan Data Tahun
                </label>
            </div>

            <!-- Dropdown -->
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select
                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
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
        </div>

        <!-- Bagian Kanan: Tombol Aksi -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            @can('create', App\Models\Kegiatan::class)
                <!-- Tombol Liat MPH -->
                <button
                    class="flex items-center justify-center gap-2 rounded-full border border-gray-300
                        bg-white px-4 py-3 text-sm font-medium text-gray-700
                        shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                        transition-colors duration-200 w-full sm:w-auto
                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                    @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-mph',
                })">
                    <!-- icon mata -->
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"
                            fill="currentColor" />
                        <path d="M12 9c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor" />
                    </svg>
                    Lihat MPH
                </button>

                <!-- Tombol Tambah Kegiatan -->
                <button
                    class="flex items-center justify-center gap-2 rounded-full border border-gray-300
                        bg-white px-4 py-3 text-sm font-medium text-gray-700
                        shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                        transition-colors duration-200 w-full sm:w-auto
                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                    @click="$dispatch('open-smart-modal', {
                            modalId: 'modal-kegiatan',
                })">
                    <!-- icon -->
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                            fill="" />
                    </svg>
                    Tambah Kegiatan
                </button>
            @endcan
        </div>
    </div>

    <!-- Container untuk Card Kegiatan -->
    <div class="space-y-6">
        {{-- {{ $kegiatans->last()->nama_rk_kegiatan }}
        {{ $kegiatans->last()->subKegiatans->sum(function ($sub) {
            return $sub->penugasans->count();
        }) }}
        {{ $kegiatans->last()->subKegiatans->sum(function ($sub) {
            return $sub->penugasans->filter(function ($p) {
                return $p->latestPengiriman?->penerimaan?->status === "Diterima";
            })->count();
        }) }} --}}
        @foreach ($kegiatans as $kegiatan)
            <!-- CARD PER KEGIATAN dengan Accordion -->
            <div x-data="{ openSubKegiatan: false }" class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-gray-800 dark:bg-gray-900">
                <!-- HEADER CARD (Sebagai Tombol Accordion) -->
                <div class="bg-white px-6 py-4 border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1">
                            <!-- Tombol Accordion untuk Nama Kegiatan -->
                            <button
                                @click="openSubKegiatan = !openSubKegiatan"
                                class="flex items-center gap-3 w-full sm:w-auto text-left group">
                                <!-- Icon Chevron -->
                                <svg
                                    class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                    :class="{ 'rotate-90': openSubKegiatan }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $kegiatan->nama_rk_kegiatan }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1 dark:text-gray-300">
                                        Ketua: {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                    </p>
                                </div>
                            </button>
                        </div>

                        <!-- ACTION BUTTON -->
                        <div class="flex flex-wrap gap-2">
                            @can('update', $kegiatan)
                                {{-- Edit --}}
                                <button
                                    class="flex items-center gap-2 rounded-full border border-gray-300
                                        bg-white px-4 py-3 text-sm font-medium text-gray-700
                                        shadow-theme-xs hover:bg-yellow-50 hover:text-yellow-700
                                        hover:border-yellow-300 transition-all duration-200
                                        dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-yellow-900/30 dark:hover:text-yellow-400"
                                    @click.stop="$dispatch('open-smart-modal', {
                                            modalId: 'modal-kegiatan',
                                            mode: 'edit',
                                            key: '{{ $kegiatan->id_kegiatan }}',
                                            data: {
                                                id_kegiatan: '{{ $kegiatan->id_kegiatan }}',
                                                nama_rk_kegiatan: @js($kegiatan->nama_rk_kegiatan),
                                                tahun_kegiatan: '{{ $kegiatan->tahun_kegiatan }}',
                                                id_penanggung_jawab: '{{ $kegiatan->id_penanggung_jawab }}',
                                                nama_penanggung_jawab: @js($kegiatan->penanggungJawab->nama_pegawai),
                                                rk_jpt: '{{ $kegiatan->rk_jpt }}',
                                                iki_jpt: '{{ $kegiatan->iki_jpt }}'
                                            }
                                        })">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg> Edit
                                </button>
                            @endcan

                            @can('delete', $kegiatan)
                                {{-- Hapus --}}
                                <form id="delete-kegiatan-{{ $kegiatan->id_kegiatan }}"
                                    action="{{ route('kegiatan.delete', [
                                        'kegiatan' => $kegiatan->id_kegiatan,
                                    ]) }}"
                                    method="POST" class="flex flex-col items-center">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                        onclick="SwalHelper.confirmDelete(
                                            'delete-kegiatan-{{ $kegiatan->id_kegiatan }}',
                                            '{{ $kegiatan->nama_rk_kegiatan }}'
                                        )"
                                        class="flex items-center gap-2 rounded-full border border-gray-300
                                        bg-white px-4 py-3 text-sm font-medium text-gray-700
                                        shadow-theme-xs hover:bg-red-50 hover:text-red-700
                                        hover:border-red-300 transition-all duration-200
                                        dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-red-900/30 dark:hover:text-red-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="text-xs font-medium">Hapus</span>
                                    </button>
                                </form>
                            @endcan

                            @can('createSubKegiatan', $kegiatan)
                                {{-- Tambah Sub --}}
                                <button
                                    class="flex items-center gap-2 rounded-full border border-gray-300
                                    bg-white px-4 py-3 text-sm font-medium text-gray-700
                                    shadow-theme-xs hover:bg-green-50 hover:text-green-700
                                    hover:border-green-300 transition-all duration-200
                                    dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-green-900/30 dark:hover:text-green-400"
                                    @click.stop="$dispatch('open-smart-modal', {
                                        modalId: 'modal-sub-kegiatan',
                                        data: {
                                            id_kegiatan: '{{ $kegiatan->id_kegiatan }}',
                                            nama_rk_kegiatan: '{{ $kegiatan->nama_rk_kegiatan }}'
                                        }
                                    })">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg> Sub Kegiatan
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- ACCORDION CONTENT: Tabel Sub Kegiatan -->
                <div
                    x-show="openSubKegiatan"
                    x-collapse
                    x-cloak
                    class="transition-all duration-300">
                    @include('pages.main.components.tables.tagihan-kerja.table-sub-kegiatan')
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL KEGIATAN --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-kegiatan')

    {{-- MODAL MPH --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-mph')

    {{-- MODAL SUB KEGIATAN --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-sub-kegiatan')

    {{-- MODAL CKP (Universal, dipakai untuk CKP Pegawai dan CKP Ketua Tim) --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-ckp-universal')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        // FUNGSI LOAD IKI BERDASARKAN RK (untuk modal kegiatan)
        async function loadIkiByRk(rkId, formData) {
            const selectEl = document.getElementById('iki_jpt');
            const hiddenEl = document.getElementById('iki_jpt_hidden');

            if (!rkId) {
                if (selectEl) selectEl.innerHTML = '<option value="">-- Harap pilih RK JPT dulu --</option>';
                if (hiddenEl) hiddenEl.value = '';
                return;
            }

            if (selectEl) {
                selectEl.innerHTML = '<option value="">Memuat...</option>';
                selectEl.disabled = true;
            }

            try {
                const res = await fetch(`/rencana-indikator-jpt/${rkId}/indikator`);
                const data = await res.json();

                if (!selectEl) return;

                const pending = formData._pendingIndikatorId ? String(formData._pendingIndikatorId) : '';
                if (formData._pendingIndikatorId !== undefined) formData._pendingIndikatorId = '';

                let html = '<option value="">-- Pilih IKI JPT --</option>';
                data.forEach(iki => {
                    const selected = String(iki.id) === pending ? 'selected' : '';
                    html += `<option value="${iki.id}" ${selected}>${iki.nama_indikator_jpt}</option>`;
                });

                selectEl.innerHTML = html;
                selectEl.disabled = false;

                if (hiddenEl) hiddenEl.value = selectEl.value;

            } catch (error) {
                if (selectEl) {
                    selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
                    selectEl.disabled = false;
                }
            }
        }

        // LISTENER EDIT MODAL KEGIATAN
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('open-smart-modal', function(e) {
                const detail = e.detail;
                if (detail.modalId !== 'modal-kegiatan' || detail.mode !== 'edit') return;
                if (!detail.data?.rk_jpt) return;

                window._kegiatanEditPending = {
                    rkId: String(detail.data.rk_jpt),
                    indikatorId: String(detail.data.iki_jpt || '')
                };

                const checkAndLoad = (attempt) => {
                    const selectEl = document.getElementById('iki_jpt');
                    if (!selectEl) {
                        if (attempt < 20) requestAnimationFrame(() => checkAndLoad(attempt + 1));
                        return;
                    }

                    const modalEl = document.getElementById('modal-kegiatan');
                    const isVisible = modalEl && getComputedStyle(modalEl).display !== 'none';

                    if (isVisible && window._kegiatanEditPending) {
                        const { rkId, indikatorId } = window._kegiatanEditPending;
                        window._kegiatanEditPending = null;
                        loadIkiByRk(rkId, { _pendingIndikatorId: indikatorId });
                    } else if (attempt < 30) {
                        requestAnimationFrame(() => checkAndLoad(attempt + 1));
                    }
                };

                requestAnimationFrame(() => checkAndLoad(0));
            });
        });
    </script>
@endsection
