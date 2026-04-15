@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <!-- Tabs & Actions -->
    <div x-data="{ activeTab: 'active' }">
        <div class="flex flex-row items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
            <!-- Tabs -->
            <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl dark:bg-gray-800">
                <button @click="activeTab = 'active'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                    :class="activeTab === 'active' 
                        ? 'bg-white text-brand-600 shadow-sm dark:bg-gray-700 dark:text-brand-400' 
                        : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'">
                    Aktif
                </button>
                <button @click="activeTab = 'inactive'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                    :class="activeTab === 'inactive' 
                        ? 'bg-white text-brand-600 shadow-sm dark:bg-gray-700 dark:text-brand-400' 
                        : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'">
                    Nonaktif
                </button>
            </div>

            <!-- Tombol Tambah Pengumuman -->
            <button @click="$dispatch('open-smart-modal', { modalId: 'modal-announcement', mode: 'create' })"
                class="gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                + Tambah Pengumuman
            </button>
        </div>

        <!-- MODAL CRUD PENGUMUMAN -->
        <x-ui.smart-modal id="modal-announcement" class="max-w-2xl"
            @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-announcement') return;
            mode = $event.detail.mode ?? 'create';
            itemKey = $event.detail.key ?? null;
            
            let baseData = $event.detail.data || {};
            formData = {
                title: '',
                content: '',
                end_date: '',
                image_preview: null,
                ...baseData
            };
        ">

            <form :action="mode === 'edit' ? `/admin/announcements/${itemKey}` : `{{ route('announcements.store') }}`" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="grid grid-cols-1 gap-y-5">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="relative flex h-[85vh] w-full max-w-[600px] flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">
                    
                    <!-- HEADER -->
                    <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white"
                            x-text="mode === 'create' ? 'Tambah Pengumuman' : 'Edit Pengumuman'"></h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                            x-text="mode === 'create' ? 'Buat pengumuman baru untuk ditampilkan' : 'Edit pengumuman yang sudah ada'">
                        </p>
                    </div>

                    <!-- BODY -->
                    <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                        
                        <!-- Judul -->
                        <div class="mb-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Judul Pengumuman <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="formData.title" name="title" id="title"
                                placeholder="Masukkan judul pengumuman"
                                class="h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-brand-300 focus:ring-2 focus:ring-brand-500/20" />
                        </div>

                        <!-- Upload Gambar - Style Baru -->
                        <div class="mb-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Gambar Pengumuman <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="relative">
                                <input type="file" name="image" id="image" accept="image/*" class="hidden"
                                    @change="if ($event.target.files.length) { 
                                        const file = $event.target.files[0]; 
                                        const reader = new FileReader();
                                        reader.onload = (e) => formData.image_preview = e.target.result;
                                        reader.readAsDataURL(file);
                                    }">
                                
                                <label for="image" 
                                    class="flex min-h-[120px] w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition-all hover:border-brand-400 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-brand-500 dark:hover:bg-gray-700">
                                    
                                    <div x-show="!formData.image_preview && !(mode === 'edit' && formData.image_url)" class="text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                            Klik untuk upload gambar
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                            JPG, JPEG, PNG, GIF (Max 5MB)
                                        </p>
                                    </div>
                                    
                                    <div x-show="formData.image_preview" class="relative w-full">
                                        <img :src="formData.image_preview" class="max-h-48 mx-auto rounded-lg" />
                                        <button type="button" @click.stop="formData.image_preview = null; document.getElementById('image').value = ''"
                                            class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white shadow-lg hover:bg-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div x-show="!formData.image_preview && mode === 'edit' && formData.image_url" class="relative w-full">
                                        <img :src="formData.image_url" class="max-h-48 mx-auto rounded-lg" />
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Tanggal Berakhir -->
                        <div class="mb-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tanggal Berakhir <span class="text-red-500">*</span>
                            </label>
                            <x-form.date-picker id="end_date" name="end_date" x-model="formData.end_date" placeholder="Pilih Tanggal" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Pengumuman akan otomatis nonaktif setelah tanggal ini
                            </p>
                        </div>

                        <!-- Konten -->
                        <div class="mb-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Isi Pengumuman <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="formData.content" name="content" id="content" rows="4"
                                placeholder="Tulis isi pengumuman di sini..."
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-brand-300 focus:ring-2 focus:ring-brand-500/20"></textarea>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="shrink-0 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <button @click="open = false" type="button"
                                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 dark:bg-brand-600 dark:hover:bg-brand-700">
                                <span x-text="mode === 'create' ? 'Simpan Pengumuman' : 'Update Pengumuman'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </x-ui.smart-modal>

        <!-- TABEL PENGUMUMAN AKTIF -->
        <div x-show="activeTab === 'active'" x-cloak>
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12 dark:text-gray-400">No.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40 dark:text-gray-400">Gambar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32 dark:text-gray-400">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @php $activeIndex = 0; @endphp
                        @forelse($announcements as $announcement)
                            @php
                                $isActive = $announcement->is_active && $announcement->start_date->isPast() && $announcement->end_date->isFuture();
                            @endphp
                            @if($isActive)
                                @php $activeIndex++; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $activeIndex }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                        <div class="max-w-xs font-medium">{{ $announcement->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ Str::limit($announcement->content, 60) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <img src="{{ asset('storage/' . $announcement->image_path) }}" class="h-12 w-20 object-cover rounded border dark:border-gray-700" />
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        <div>{{ $announcement->start_date->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">sd</div>
                                        <div>{{ $announcement->end_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="relative inline-block group">
                                            <!-- Button Aksi -->
                                            <button class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-green-400 hover:text-green-600 transition-all duration-200 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-green-500 dark:hover:text-green-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Aksi
                                            </button>

                                            <!-- Dropdown -->
                                            <div class="absolute right-0 top-full mt-1 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                                <div class="py-1">
                                                    <!-- Toggle Status -->
                                                    <form action="{{ route('announcements.toggle', $announcement) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                            Nonaktifkan
                                                        </button>
                                                    </form>

                                                    <!-- Edit -->
                                                    <button @click="$dispatch('open-smart-modal', {
                                                        modalId: 'modal-announcement',
                                                        mode: 'edit',
                                                        key: '{{ $announcement->id }}',
                                                        data: {{ Js::from([
                                                            'title' => $announcement->title,
                                                            'content' => $announcement->content,
                                                            'end_date' => $announcement->end_date->format('Y-m-d'),
                                                            'image_url' => $announcement->image_path 
                                                                ? 'https://pindangoi.bpsoganilir.com/storage/' . $announcement->image_path 
                                                                : null,
                                                        ]) }}
                                                    })" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    <!-- Delete -->
                                                    <form id="delete-announcement-{{ $announcement->id }}" action="{{ route('announcements.destroy', $announcement) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDeleteAnnouncement('delete-announcement-{{ $announcement->id }}', '{{ addslashes($announcement->title) }}')"
                                                            class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            @php $hasActive = false; @endphp
                        @endforelse
                        
                        @if($announcements->where('is_active', true)->filter(function($item) { return $item->start_date->isPast() && $item->end_date->isFuture(); })->count() == 0)
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada pengumuman aktif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL PENGUMUMAN NONAKTIF -->
        <div x-show="activeTab === 'inactive'" x-cloak>
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12 dark:text-gray-400">No.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40 dark:text-gray-400">Gambar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32 dark:text-gray-400">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @php $inactiveIndex = 0; @endphp
                        @forelse($announcements as $announcement)
                            @php
                                $isActive = $announcement->is_active && $announcement->start_date->isPast() && $announcement->end_date->isFuture();
                            @endphp
                            @if(!$isActive)
                                @php $inactiveIndex++; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">{{ $inactiveIndex }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                        <div class="max-w-xs font-medium">{{ $announcement->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ Str::limit($announcement->content, 60) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <img src="{{ asset('storage/' . $announcement->image_path) }}" class="h-12 w-20 object-cover rounded border dark:border-gray-700" />
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        <div>{{ $announcement->start_date->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">sd</div>
                                        <div>{{ $announcement->end_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Nonaktif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="relative inline-block group">
                                            <!-- Button Aksi -->
                                            <button class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-green-400 hover:text-green-600 transition-all duration-200 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-green-500 dark:hover:text-green-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Aksi
                                            </button>

                                            <!-- Dropdown -->
                                            <div class="absolute right-0 top-full mt-1 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                                <div class="py-1">
                                                    <!-- Toggle Status -->
                                                    <form action="{{ route('announcements.toggle', $announcement) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Aktifkan
                                                        </button>
                                                    </form>

                                                    <!-- Edit -->
                                                    <button @click="$dispatch('open-smart-modal', {
                                                        modalId: 'modal-announcement',
                                                        mode: 'edit',
                                                        key: '{{ $announcement->id }}',
                                                        data: {{ Js::from([
                                                            'title' => $announcement->title,
                                                            'content' => $announcement->content,
                                                            'end_date' => $announcement->end_date->format('Y-m-d'),
                                                            'image_url' => $announcement->image_path 
                                                                ? 'https://pindangoi.bpsoganilir.com/storage/' . $announcement->image_path 
                                                                : null,
                                                        ]) }}
                                                    })" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    <!-- Delete -->
                                                    <form id="delete-announcement-inactive-{{ $announcement->id }}" action="{{ route('announcements.destroy', $announcement) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDeleteAnnouncement('delete-announcement-inactive-{{ $announcement->id }}', '{{ addslashes($announcement->title) }}')"
                                                            class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            @php $hasInactive = false; @endphp
                        @endforelse
                        
                        @if($announcements->where('is_active', true)->filter(function($item) { return $item->start_date->isPast() && $item->end_date->isFuture(); })->count() == $announcements->count())
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada pengumuman nonaktif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function confirmDeleteAnnouncement(formId, title) {
            SwalHelper.confirmDelete(formId, title);
        }
    </script>
@endsection