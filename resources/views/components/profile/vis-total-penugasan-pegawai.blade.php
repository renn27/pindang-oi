<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- TOTAL PENUGASAN -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Total Penugasan</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">
                    {{ $totalpenugasanPegawai['total'] }}
                </p>
            </div>
            <div class="rounded-full bg-blue-100 p-3 text-blue-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- BELUM MULAI -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Belum Mulai</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">
                    {{ $totalpenugasanPegawai['belum_mulai'] }}
                </p>
            </div>
            <div class="rounded-full bg-yellow-100 p-3 text-yellow-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- SEDANG BERJALAN -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Sedang Berjalan</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">
                    {{ $totalpenugasanPegawai['sedang_berjalan'] }}
                </p>
            </div>
            <div class="rounded-full bg-indigo-100 p-3 text-indigo-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- SUDAH SELESAI -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Sudah Selesai</p>
                <p class="mt-1 text-2xl font-bold text-gray-800">
                    {{ $totalpenugasanPegawai['sudah_selesai'] }}
                </p>
            </div>
            <div class="rounded-full bg-green-100 p-3 text-green-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>
</div>


