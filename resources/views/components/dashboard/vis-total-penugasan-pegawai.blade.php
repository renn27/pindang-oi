<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">

    <!-- Background Balls untuk Glassmorphism -->
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <!-- Balls untuk card 1 - Biru -->
        <div class="absolute -top-12 -left-12 w-52 h-52 bg-gradient-to-br from-blue-400/30 to-blue-600/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute top-32 right-16 w-40 h-40 bg-gradient-to-tr from-purple-400/25 to-blue-500/20 rounded-full blur-3xl animate-float"></div>

        <!-- Balls untuk card 2 - Kuning -->
        <div class="absolute top-1/3 left-1/4 w-48 h-48 bg-gradient-to-br from-yellow-400/30 to-orange-500/25 rounded-full blur-3xl animate-pulse-medium"></div>

        <!-- Balls untuk card 3 - Ungu -->
        <div class="absolute top-1/2 right-1/3 w-56 h-56 bg-gradient-to-tr from-indigo-400/35 to-purple-600/30 rounded-full blur-3xl animate-float-slow"></div>

        <!-- Balls untuk card 4 - Hijau -->
        <div class="absolute bottom-12 left-1/3 w-44 h-44 bg-gradient-to-br from-green-400/35 to-emerald-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute -bottom-8 right-24 w-40 h-40 bg-gradient-to-tr from-emerald-400/25 to-teal-500/20 rounded-full blur-3xl animate-float"></div>
    </div>

    <!-- TOTAL PENUGASAN -->
    <div class="rounded-2xl border border-white/30 bg-white/25 backdrop-blur-xl p-6 shadow-lg shadow-blue-200/20 dark:shadow-blue-900/10 dark:border-blue-500/20 dark:bg-gray-900/40 relative overflow-hidden group hover:shadow-blue-300/30 dark:hover:shadow-blue-700/20 transition-all duration-300 hover:-translate-y-1">
        <!-- Ball besar dalam card -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-gradient-to-br from-blue-400/25 to-blue-600/15 rounded-full blur-2xl animate-pulse-slow"></div>
        <!-- Ball kecil dalam card -->
        <div class="absolute bottom-8 -left-8 w-32 h-32 bg-gradient-to-tr from-blue-300/20 to-cyan-400/15 rounded-full blur-2xl animate-float-slow"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-700 dark:text-gray-300/95 font-medium">Total Penugasan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $totalpenugasanPegawai['total'] }}
                </p>
            </div>
            <div class="rounded-full bg-white/70 p-3 text-blue-600 shadow-md group-hover:scale-110 transition-transform duration-300 dark:bg-white/20 dark:text-blue-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- BELUM MULAI -->
    <div class="rounded-2xl border border-white/30 bg-white/25 backdrop-blur-xl p-6 shadow-lg shadow-yellow-200/20 dark:shadow-yellow-900/10 dark:border-yellow-500/20 dark:bg-gray-900/40 relative overflow-hidden group hover:shadow-yellow-300/30 dark:hover:shadow-yellow-700/20 transition-all duration-300 hover:-translate-y-1">
        <!-- Ball besar dalam card -->
        <div class="absolute -bottom-20 -left-16 w-56 h-56 bg-gradient-to-tr from-yellow-400/30 to-orange-500/20 rounded-full blur-2xl animate-pulse-medium"></div>
        <!-- Ball kecil dalam card -->
        <div class="absolute -top-10 right-8 w-36 h-36 bg-gradient-to-br from-amber-300/25 to-orange-400/20 rounded-full blur-2xl animate-float"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-700 dark:text-gray-300/95 font-medium">Belum Mulai</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $totalpenugasanPegawai['belum_mulai'] }}
                </p>
            </div>
            <div class="rounded-full bg-white/70 p-3 text-yellow-600 shadow-md group-hover:scale-110 transition-transform duration-300 dark:bg-white/20 dark:text-yellow-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M12 8v4l3 3M12 22a10 10 0 100-20 10 10 0 000 20z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- SEDANG BERJALAN -->
    <div class="rounded-2xl border border-white/30 bg-white/25 backdrop-blur-xl p-6 shadow-lg shadow-indigo-200/20 dark:shadow-indigo-900/10 dark:border-indigo-500/20 dark:bg-gray-900/40 relative overflow-hidden group hover:shadow-indigo-300/30 dark:hover:shadow-indigo-700/20 transition-all duration-300 hover:-translate-y-1">
        <!-- Ball besar dalam card -->
        <div class="absolute -top-12 right-12 w-52 h-52 bg-gradient-to-bl from-indigo-400/35 to-purple-600/25 rounded-full blur-2xl animate-pulse-slow"></div>
        <!-- Ball kecil dalam card -->
        <div class="absolute bottom-12 -left-8 w-40 h-40 bg-gradient-to-tr from-purple-300/30 to-indigo-400/25 rounded-full blur-2xl animate-float-slow"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-700 dark:text-gray-300/95 font-medium">Sedang Berjalan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $totalpenugasanPegawai['sedang_berjalan'] }}
                </p>
            </div>
            <div class="rounded-full bg-white/70 p-3 text-indigo-600 shadow-md group-hover:scale-110 transition-transform duration-300 dark:bg-white/20 dark:text-indigo-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- SUDAH SELESAI -->
    <div class="rounded-2xl border border-white/30 bg-white/25 backdrop-blur-xl p-6 shadow-lg shadow-green-200/20 dark:shadow-green-900/10 dark:border-green-500/20 dark:bg-gray-900/40 relative overflow-hidden group hover:shadow-green-300/30 dark:hover:shadow-green-700/20 transition-all duration-300 hover:-translate-y-1">
        <!-- Ball besar dalam card -->
        <div class="absolute top-12 -right-12 w-56 h-56 bg-gradient-to-bl from-green-400/40 to-emerald-600/30 rounded-full blur-2xl animate-pulse-medium"></div>
        <!-- Ball kecil dalam card -->
        <div class="absolute -bottom-16 left-12 w-44 h-44 bg-gradient-to-tr from-emerald-300/30 to-green-400/25 rounded-full blur-2xl animate-float"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-700 dark:text-gray-300/95 font-medium">Sudah Selesai</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $totalpenugasanPegawai['sudah_selesai'] }}
                </p>
            </div>
            <div class="rounded-full bg-white/70 p-3 text-green-600 shadow-md group-hover:scale-110 transition-transform duration-300 dark:bg-white/20 dark:text-green-300">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>
</div>
