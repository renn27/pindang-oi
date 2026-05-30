@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <div
        x-data="{
            searchPegawai: '',
            normalize(value) {
                return String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            },
            matches(value) {
                const term = this.normalize(this.searchPegawai.trim());
                return term === '' || this.normalize(value).includes(term);
            },
            visibleCount() {
                return Array.from(this.$refs.pegawaiRows?.querySelectorAll('[data-search]') ?? [])
                    .filter((row) => this.matches(row.dataset.search))
                    .length;
            }
        }">
        <div class="mb-4 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:flex-row lg:items-center">
            <div class="relative w-full lg:max-w-md">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.5 17.5L13.875 13.875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <input type="search" x-model.debounce.150ms="searchPegawai"
                    placeholder="Cari pegawai..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-white pl-10 pr-10 text-sm text-gray-800 outline-none transition-colors placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                <button type="button" x-show="searchPegawai" x-cloak @click="searchPegawai = ''"
                    class="absolute right-2 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                    aria-label="Bersihkan pencarian">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M6 6L14 14M14 6L6 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="flex items-center lg:flex-1">
                <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <span class="font-semibold text-gray-800 dark:text-white" x-text="visibleCount()"></span>
                    <span class="ml-1">pegawai ditampilkan</span>
                </span>
            </div>

            <button type="button"
                @click="$dispatch('open-smart-modal', { modalId: 'modal-create-pegawai' })"
                class="inline-flex h-11 items-center justify-center rounded-lg bg-blue-600 px-5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 lg:ml-auto">
                Tambah Pegawai
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
                            Username
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Role
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                            Status
                        </th>
                        <th class="px-4 py-3 w-64 text-center text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody x-ref="pegawaiRows" class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($pegawais as $i => $pegawai)
                        <tr
                            data-search="{{ $pegawai->nama_pegawai }} {{ $pegawai->username }} {{ $pegawai->jabatan }}"
                            x-show="matches($el.dataset.search)"
                            x-cloak
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition {{ $pegawai->is_active ? '' : 'opacity-70' }}">
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

                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $pegawai->username }}
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $roleColors = [
                                        'Admin' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
                                        'Pimpinan' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
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
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium border {{ $roleColors[$badge] ?? $roleColors['Belum Ada'] }}">
                                            {{ $badge }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($pegawai->is_active)
                                    <span class="inline-flex rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-medium text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        Aktif
                                    </span>
                                @else
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex rounded-full border border-gray-300 bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Nonaktif
                                        </span>
                                        @if ($pegawai->inactive_from_month)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                sejak {{ $pegawai->inactive_from_month->format('m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white hover:border-brand-400 hover:text-brand-600 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300"
                                        @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-assign-role',
                                            mode: 'edit',
                                            key: '{{ $pegawai->id_pegawai }}',
                                            data: {
                                                id_pegawai: '{{ $pegawai->id_pegawai }}',
                                                nama_pegawai: '{{ $pegawai->nama_pegawai }}',
                                                roles: @js($pegawai->roles->pluck('id'))
                                            }
                                        })">
                                        Edit Role
                                    </button>

                                    @if ($pegawai->is_active)
                                        <button type="button"
                                            @disabled($pegawai->id_pegawai === auth()->user()->id_pegawai)
                                            @click="$dispatch('open-smart-modal', {
                                                modalId: 'modal-toggle-active-pegawai',
                                                key: '{{ $pegawai->id_pegawai }}',
                                                data: {
                                                    action: '{{ route('pegawai-role.toggle-active', $pegawai->id_pegawai) }}',
                                                    nama_pegawai: @js($pegawai->nama_pegawai),
                                                    inactive_month: {{ now()->month }},
                                                    inactive_year: {{ now()->year }}
                                                }
                                            })"
                                            class="inline-flex items-center rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-red-800 dark:text-red-300">
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('pegawai-role.toggle-active', $pegawai->id_pegawai) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg border border-green-200 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-50 dark:border-green-800 dark:text-green-300">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr x-show="visibleCount() === 0" x-cloak>
                        <td colspan="6" class="px-4 py-10 text-center">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai tidak ditemukan</div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Coba gunakan nama atau username lain.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <x-ui.smart-modal id="modal-toggle-active-pegawai" class="max-w-lg">
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">Nonaktifkan Pegawai</h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="'Pilih bulan mulai nonaktif untuk ' + (formData.nama_pegawai || 'pegawai ini') + '.'"></p>
        </div>

        <form :action="formData.action" method="POST" class="space-y-5 px-6 py-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Mulai nonaktif dari bulan
                </label>

                <input type="hidden" name="inactive_from_month"
                    :value="formData.inactive_year + '-' + String(formData.inactive_month).padStart(2, '0')">

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
                    <div class="mb-3">
                        <select x-model.number="formData.inactive_year"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            @for ($year = now()->year - 5; $year <= now()->year + 2; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                        @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $index => $monthName)
                            <button type="button"
                                @click="formData.inactive_month = {{ $index + 1 }}"
                                :class="Number(formData.inactive_month) === {{ $index + 1 }}
                                    ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                                    : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-blue-700 dark:hover:bg-blue-900/20'"
                                class="h-10 rounded-lg border text-sm font-medium transition-colors">
                                {{ $monthName }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Pegawai tidak akan muncul di rekap penugasan mulai bulan yang dipilih.
                </p>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-700">
                <button type="button" @click="open=false"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-300">
                    Batal
                </button>
                <button type="submit"
                    class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700">
                    Nonaktifkan
                </button>
            </div>
        </form>
    </x-ui.smart-modal>

    <x-ui.smart-modal id="modal-create-pegawai" class="max-w-2xl">
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">Tambah Pegawai</h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat akun pegawai dan tentukan role awalnya.</p>
        </div>

        <div class="flex-1 overflow-y-auto max-h-[calc(100vh-180px)]">
            <form action="{{ route('pegawai-role.pegawai-store') }}" method="POST" class="space-y-5 px-6 py-5">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pegawai</label>
                        <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai') }}" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" name="password" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Role Pegawai</label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($roles as $role)
                            <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 dark:border-gray-700">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    @checked(in_array($role->id, old('roles', [])))
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-300">{{ $role->nama_role }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-700">
                    <button type="button" @click="open=false"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-300">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                        Simpan Pegawai
                    </button>
                </div>
            </form>
        </div>
    </x-ui.smart-modal>

    <x-ui.smart-modal id="modal-assign-role" class="max-w-xl"
        @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-assign-role') return;

        mode     = $event.detail.mode ?? 'create'
        itemKey  = $event.detail.key ?? null
        formData = $event.detail.data ?? { id_pegawai: '', nama_pegawai: '', roles: [] }">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Role Pegawai</h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit role yang sudah ada</p>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto max-h-[calc(100vh-200px)]">
            <form
                :action="mode === 'edit'
                    ? `{{ url('role-pegawai') }}/${itemKey}`
                    : `{{ route('pegawai-role.store') }}`"
                method="POST" class="px-6 py-5 space-y-6">
                @csrf

                <!-- METHOD SPOOFING SAAT EDIT -->
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="POST">
                </template>

                {{-- Nama Anggota --}}
                <div x-data="pegawaiDropdown()"
                    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-assign-role') return;
                        initFromModal($event.detail);
                    "
                    class="space-y-2">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Pegawai
                    </label>

                    <!-- Hidden ID Pegawai (WAJIB buat submit) -->
                    <input type="hidden" name="id_pegawai" x-model="selectedId">

                    <!-- Input Visible -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @click="mode === 'create' && (open = true)"
                            @input="mode === 'create' && (open = true)"
                            @keydown.escape="open = false"
                            :readonly="mode === 'edit'"
                            placeholder="Pilih pegawai..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">

                        <!-- Dropdown -->
                        <div x-show="open && mode === 'create'"
                            x-transition
                            @click.outside="open = false"
                            class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto
                                    rounded-lg border border-gray-200 bg-white shadow-lg
                                    dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="filteredPegawais.length === 0">
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">
                                    Pegawai tidak ditemukan
                                </div>
                            </template>

                            <template x-for="pegawai in filteredPegawais" :key="pegawai.id_pegawai">
                                <button type="button"
                                        @click="selectPegawai(pegawai)"
                                        class="flex w-full items-center px-4 py-3 text-left text-sm
                                            hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                    <div class="font-medium text-gray-800 dark:text-gray-200"
                                        x-text="pegawai.nama_pegawai"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- ROLE PEGAWAI -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Role Pegawai
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($roles as $role)
                                <label
                                    class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3.5
                                    hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200">
                                    <input type="checkbox" 
                                        name="roles[]" 
                                        value="{{ $role->id }}"
                                        :checked="formData.roles && formData.roles.includes({{ $role->id }})"
                                        class="h-5 w-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                                                text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:checked:bg-blue-600">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                        {{ $role->nama_role }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- FOOTER BUTTONS -->
                <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-5">
                    <button type="button" @click="open=false"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                        px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                        hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </button>

                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                        <span x-text="mode === 'create' ? 'Simpan Role' : 'Update Role'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-ui.smart-modal>

    <script>
        function pegawaiDropdown() {
            return {
                open: false,
                search: '',
                selectedId: '',
                mode: 'create',

                pegawais: @js(
                    $pegawais->map(fn($p) => [
                        'id_pegawai'   => $p->id_pegawai,
                        'nama_pegawai' => $p->nama_pegawai
                    ])
                ),

                initFromModal(detail) {
                    this.mode = detail.mode ?? 'create';

                    if (this.mode === 'edit') {
                        this.selectedId = detail.data.id_pegawai;
                        this.search     = detail.data.nama_pegawai;
                        this.open       = false;
                    } else {
                        this.selectedId = '';
                        this.search     = '';
                        this.open       = true;
                    }
                },

                get filteredPegawais() {
                    if (!this.search) return this.pegawais;

                    return this.pegawais.filter(p =>
                        p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                selectPegawai(p) {
                    this.selectedId = p.id_pegawai;
                    this.search     = p.nama_pegawai;
                    this.open       = false;
                }
            }
        }
    </script>
@endsection
