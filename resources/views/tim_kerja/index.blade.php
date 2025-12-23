@extends('layouts.app')

@section('content')

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
    <!-- header -->
    <div class="flex flex-col gap-4 px-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
            Data Tim Kerja
        </h3>

        <div class="flex items-center gap-4">
            <div class="relative">
                <input
                    id="searchTim"
                    type="text"
                    placeholder="Search..."
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 sm:w-64">
            </div>

            <button
                id="btnAdd"
                class="flex justify-center items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 whitespace-nowrap h-10">
                + Tambah Tim
            </button>
        </div>
    </div>

    <!-- table -->
    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table id="tim-kerja-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16 border-r border-gray-200 dark:border-gray-700">
                            No
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                            Nama Tim
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                            Id Ketua
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                            Dibuat
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-r border-gray-200 dark:border-gray-700">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Data akan diisi oleh DataTables -->
                </tbody>
            </table>

            <div id="pagination-wrapper" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <!-- Pagination akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>
</div>
<x-ui.modal
    x-data="{ open: false, mode: 'add' }"
    @open-tim-kerja-modal.window="open = true; mode = 'add'; $('#namaTim').val(''); $('#idKetua').val(''); $('#timId').val('');"
    @open-edit-tim-kerja-modal.window="open = true; mode = 'edit';"
    :isOpen="false"
    class="max-w-[400px]">
    
    <div
        class="relative flex h-[90vh] max-h-[600px] w-full max-w-[400px] flex-col overflow-hidden
               rounded-3xl bg-white dark:bg-gray-900">

        <!-- HEADER (FIXED) -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h4 x-text="mode === 'add' ? 'Tambah Tim' : 'Edit Tim'" 
                class="text-xl font-semibold text-gray-800 dark:text-white/90">
            </h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                <span x-text="mode === 'add' ? 'Tambahkan' : 'Edit'"></span> data tim kerja
            </p>
        </div>

        <!-- BODY (SCROLL DI SINI) -->
        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
            <form class="grid grid-cols-1 gap-y-5">
                <!-- ID Tim (hidden) -->
                <input type="hidden" id="timId">
                
                <!-- Nama Tim -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Tim
                    </label>
                    <input
                        id="namaTim"
                        type="text"
                        placeholder="Masukkan nama tim"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <!-- ID Ketua -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        ID Ketua Tim
                    </label>
                    <input
                        id="idKetua"
                        type="text"
                        placeholder="Masukkan ID ketua tim"
                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>
            </form>
        </div>

        <!-- FOOTER (FIXED) -->
        <div
            class="shrink-0 border-t border-gray-200 px-6 py-4
                   dark:border-gray-800">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                    @click="open = false"
                    type="button"
                    class="flex w-full justify-center rounded-lg border
                           border-gray-300 bg-white px-4 py-2.5 text-sm
                           font-medium text-gray-700 hover:bg-gray-50
                           dark:border-gray-700 dark:bg-gray-800
                           dark:text-gray-400 dark:hover:bg-white/[0.03]
                           sm:w-auto">
                    Batal
                </button>

                <button
                    @click="saveTim()"
                    type="button"
                    x-text="mode === 'add' ? 'Simpan' : 'Update'"
                    class="flex w-full justify-center rounded-lg
                           bg-brand-500 px-4 py-2.5 text-sm font-medium
                           text-white hover:bg-brand-600 sm:w-auto">
                </button>
            </div>
        </div>
    </div>
</x-ui.modal>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =====================================================
        // 1. INIT DATATABLES
        // =====================================================
        const table = $('#tim-kerja-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('tim-kerja.data') }}",

            // matiin UI bawaan datatables
            dom: 'rt',
            paging: true,
            pageLength: 5,

            columns: [{
                    data: 'id_tim_kerja',
                    name: 'id_tim_kerja',
                    className: 'px-4 py-3 text-sm text-gray-900 dark:text-gray-300 text-center border-r border-gray-200 dark:border-gray-700'
                },
                {
                    data: 'nama_tim',
                    name: 'nama_tim',
                    className: 'px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700'
                },
                {
                    data: 'id_ketua',
                    name: 'id_ketua',
                    className: 'px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-r border-gray-200 dark:border-gray-700'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'px-4 py-3 text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="flex items-center justify-center gap-1">
                                <button class="editTim p-2 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-300 transition-colors" 
                                        data-id="${row.id_tim_kerja}" 
                                        data-nama="${row.nama_tim}" 
                                        data-ketua="${row.id_ketua}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button class="hapusTim p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300 transition-colors" 
                                        data-id="${row.id_tim_kerja}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    }
                },
            ],

            // Styling untuk row
            createdRow: function(row, data, dataIndex) {
                $(row).addClass('hover:bg-gray-50 dark:hover:bg-gray-800/30');
            }
        });

        // =====================================================
        // 2. SEARCH CUSTOM
        // =====================================================
        $('#searchTim').on('keyup', function() {
            table.search(this.value).draw();
        });

        // =====================================================
        // 3. PAGINATION CUSTOM
        // =====================================================
        function renderPagination() {
            const info = table.page.info();
            let pages = '';

            for (let i = 1; i <= info.pages; i++) {
                pages += `
                <li>
                    <button
                        data-page="${i - 1}"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium
                        ${info.page + 1 === i
                            ? 'bg-brand-500 text-white'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800'}">
                        ${i}
                    </button>
                </li>
            `;
            }

            $('#pagination-wrapper').html(`
            <div class="flex items-center justify-between">
                <button id="prevPage"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800
                    ${info.page === 0 ? 'opacity-50 cursor-not-allowed' : ''}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </button>

                <ul class="flex items-center gap-1">
                    ${pages}
                </ul>

                <button id="nextPage"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800
                    ${info.page + 1 === info.pages ? 'opacity-50 cursor-not-allowed' : ''}">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        `);

            $('#prevPage').on('click', () => table.page('previous').draw('page'));
            $('#nextPage').on('click', () => table.page('next').draw('page'));

            $('#pagination-wrapper button[data-page]').on('click', function() {
                table.page($(this).data('page')).draw('page');
            });
        }

        table.on('draw', renderPagination);

        // =====================================================
        // 4. ADD DATA (OPEN MODAL - ALPINE.JS)
        // =====================================================
        $('#btnAdd').on('click', function() {
            $('#timId').val('');
            $('#namaTim').val('');
            $('#idKetua').val('');
            
            // Trigger Alpine.js modal untuk tambah data
            window.dispatchEvent(new CustomEvent('open-tim-kerja-modal'));
        });

        // =====================================================
        // 5. EDIT DATA (OPEN MODAL + FILL DATA - ALPINE.JS)
        // =====================================================
        $(document).on('click', '.editTim', function() {
            $('#timId').val($(this).data('id'));
            $('#namaTim').val($(this).data('nama'));
            $('#idKetua').val($(this).data('ketua'));
            
            // Trigger Alpine.js modal untuk edit data
            window.dispatchEvent(new CustomEvent('open-edit-tim-kerja-modal'));
        });

        // =====================================================
        // 6. SAVE DATA FUNCTION (untuk Alpine.js)
        // =====================================================
        window.saveTim = function() {
            if (!$('#namaTim').val().trim()) {
                alert('Nama tim wajib diisi');
                return;
            }

            if (!$('#idKetua').val().trim()) {
                alert('Ketua tim wajib diisi');
                return;
            }

            const id = $('#timId').val();
            const url = id ? `/tim-kerja/${id}` : `/tim-kerja`;

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: id ? 'PUT' : 'POST',
                    nama_tim: $('#namaTim').val(),
                    id_ketua: $('#idKetua').val()
                },
                success: function(response) {
                    // Tutup modal Alpine.js (event ke komponen)
                    window.dispatchEvent(new CustomEvent('close-modal'));
                    
                    // Clear form
                    $('#timId').val('');
                    $('#namaTim').val('');
                    $('#idKetua').val('');
                    
                    // Reload table
                    table.ajax.reload(null, false);
                    
                    // Show success message jika diperlukan
                    // alert('Data berhasil disimpan');
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseText);
                }
            });
        };

        // =====================================================
        // 7. DELETE DATA (tetap sama)
        // =====================================================
        $(document).on('click', '.hapusTim', function() {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

            $.ajax({
                url: `/tim-kerja/${$(this).data('id')}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function() {
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Gagal menghapus data');
                    console.log(xhr.responseText);
                }
            });
        });

        // =====================================================
        // 8. TOMBOL BATAL DI MODAL (tidak perlu lagi, karena handle oleh Alpine)
        // =====================================================
        // Di handle oleh Alpine.js modal component

        // =====================================================
        // 9. CLICK OUTSIDE MODAL TO CLOSE (tidak perlu lagi)
        // =====================================================
        // Di handle oleh Alpine.js modal component
    });
</script>

@endsection