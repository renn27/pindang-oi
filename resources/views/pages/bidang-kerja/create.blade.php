@extends('layouts.app')


@section('content')
    <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
        <x-common.page-breadcrumb pageTitle="{{$title}}" />
        <form action="{{route('bidang.store')}}" method="POST" class="grid grid-cols-1 gap-y-5">
            @csrf
            <!-- sebelahan -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Tahun
                </label>
                <div class="md:w-3/4 h-11 flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    2025
                </div>
            </div>

            <!-- sebelahan -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Nama Bidang
                </label>
                <input type="text" name="nama_bidang" id="nama_bidang"
                    class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <!-- sebelahan -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Slug (Otomatis Terisi)
                </label>
                <input type="text" name="slug" id="slug"
                    class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <!-- sebelahan -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Detail Bidang
                </label>
                <input type="text" name="detail_bidang" id="detail_bidang"
                    class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <!-- FOOTER (FIXED) -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Simpan Data
                    </button>
                </div>
            </div>
        </form>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const namaInput = document.getElementById('nama_bidang');
        const slugInput = document.getElementById('slug');

        namaInput.addEventListener('input', function () {
            if (slugInput.dataset.manual === "true") return;

            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        });

        slugInput.addEventListener('input', function () {
            // Kalau slug dikosongkan → aktifkan auto lagi
            if (this.value === '' || !isempty(this.value)) {
                this.dataset.manual = "false";
            } else {
                this.dataset.manual = "true";
            }
        });
    });
    </script>

@endsection
