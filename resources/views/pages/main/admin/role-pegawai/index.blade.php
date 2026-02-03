@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <div class="mb-6 flex justify-end">
        <button class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-assign-role',
            })">
            Assign Role
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 w-16 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            No
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Nama Pegawai
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Role
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($pegawais as $i => $pegawai)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                {{ $i + 1 }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $pegawai->nama_pegawai }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $pegawai->jabatan }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $roleColors = [
                                        'Admin'     => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
                                        'Pimpinan'  => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                        'Ketua Tim' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                        'Anggota Tim' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800',
                                        'Belum Ada' => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
                                    ];

                                    $badges = collect();

                                    // 1. role struktural
                                    foreach ($pegawai->roles as $role) {
                                        $badges->push($role->nama_role);
                                    }

                                    // 2. status anggota tim (IMPLISIT)
                                    if ($pegawai->penugasanSebagaiAnggota()->exists()) {
                                        $badges->push('Anggota Tim');
                                    }

                                    // 3. fallback
                                    if ($badges->isEmpty()) {
                                        $badges->push('Belum Ada');
                                    }
                                @endphp

                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($badges->unique() as $badge)
                                        <span
                                            class="inline-flex items-center rounded-full
                                                px-3 py-1 text-xs font-medium border
                                                {{ $roleColors[$badge] ?? $roleColors['Belum Ada'] }}">
                                            {{ $badge }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <x-ui.smart-modal id="modal-assign-role" class="max-w-[700px]">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-3 dark:bg-gray-900">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">Assign Role Pegawai</h4>
        </div>

        <form action="{{ route('pegawai-role.store') }}" method="POST" class="px-6 py-5 space-y-4 dark:bg-gray-900">
            @csrf
            {{-- Nama Seluruh Pegawai --}}
            <div x-data="{
                open: false,
                search: '',
                selectedId: '',
                highlightedIndex: -1,
                pegawais: @js($pegawais),

                init() {
                    // ketika modal edit dibuka
                    if (this.$root.formData?.nama_anggota) {
                        this.search = this.$root.formData.nama_anggota;
                        this.selectedId = this.$root.formData.id_anggota;
                    }
                },

                filtered() {
                    if (this.search.length === 0) return [];
                    return this.pegawais.filter(p =>
                        p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                selectPegawai(p) {
                    this.search = p.nama_pegawai;
                    this.selectedId = p.id_pegawai;
                    // sinkron ke formData (PENTING)
                    this.$root.formData.nama_anggota = p.nama_pegawai;
                    this.$root.formData.id_anggota = p.id_pegawai;

                    this.open = false;
                    this.highlightedIndex = -1;
                },

                highlightNext() { if (this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
            }">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Pegawai
                </label>
                <!-- Input search -->
                <input type="text" x-model="search" class="h-11 w-full mb-4 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 px-4 py-2 text-sm"
                    placeholder="Ketik untuk cari nama" {{-- x-model="formData.nama_anggota" --}} @focus="open = true"
                    @input="open = true; selectedId = ''" {{-- @focus="open = !!search" @input="open = search.length > 0; selectedId = ''" --}}
                    @keydown.arrow-down.prevent="highlightedIndex++" @keydown.arrow-up.prevent="highlightedIndex--"
                    @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }">

                <!-- Hidden input -->
                <input type="hidden" name="pegawai_id" :value="selectedId" required>

                <!-- Dropdown -->
                <div x-show="open" x-transition
                    class="absolute z-50 mt-1 w-full mb-4 rounded-lg border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-800 max-h-60 overflow-y-auto">
                    <template
                        x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))"
                        :key="pegawai.id_pegawai">
                        <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                            :class="{
                                'bg-blue-100 dark:bg-blue-900/40': highlightedIndex === index,
                                'hover:bg-gray-100 dark:hover:bg-gray-700': highlightedIndex !== index
                            }"
                            class="cursor-pointer px-4 py-2 text-sm dark:text-gray-300" x-text="pegawai.nama_pegawai"></div>
                    </template>
                    <template
                        x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                        <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Data tidak ditemukan</div>
                    </template>
                </div>
            </div>

            <!-- Roles -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Role Pegawai
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($roles as $role)
                        <label
                            class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
                                hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-brand-500
                                    focus:ring-brand-500 dark:checked:bg-brand-500">
                            <span class="text-sm text-gray-800 dark:text-gray-300">
                                {{ $role->nama_role }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                <button
                    type="button"
                    @click="open=false"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2
                        text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2
                        text-sm font-medium text-white hover:bg-brand-600 dark:bg-brand-600 dark:hover:bg-brand-700">
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.smart-modal>
@endsection