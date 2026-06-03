<div class="space-y-4">
    <!-- Tabs Navigation -->
    <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700 mb-4">
        <!-- Tab: Revisi Ketua Tim -->
        <button type="button" @click="activeTabModal = 'revisi'"
            :class="{'border-orange-500 text-orange-600 dark:text-orange-400 dark:border-orange-500': activeTabModal === 'revisi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabModal !== 'revisi'}"
            class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center focus:outline-none">
            <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" :class="activeTabModal === 'revisi' ? 'text-orange-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            Revisi Ketua Tim
            @if($revisiCount > 0)
                <span class="ml-2 bg-orange-100 text-orange-700 py-0.5 px-2 rounded-full text-xs dark:bg-orange-900 dark:text-orange-200 font-bold animate-pulse">{{ $revisiCount }}</span>
            @else
                <span class="ml-2 bg-gray-100 text-gray-400 py-0.5 px-2 rounded-full text-xs dark:bg-gray-800 dark:text-gray-500 font-medium">0</span>
            @endif
        </button>

        <!-- Tab: Sedang Berjalan -->
        <button type="button" @click="activeTabModal = 'berjalan'"
            :class="{'border-blue-500 text-blue-600 dark:text-blue-500 dark:border-blue-500': activeTabModal === 'berjalan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabModal !== 'berjalan'}"
            class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center focus:outline-none">
            Sedang Berjalan
            @if(count($unfinishedBerjalanAsAnggota) > 0)
                <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs dark:bg-blue-900 dark:text-blue-200">{{ count($unfinishedBerjalanAsAnggota) }}</span>
            @endif
        </button>

        <!-- Tab: Sudah Terlewat -->
        <button type="button" @click="activeTabModal = 'terlewat'"
            :class="{'border-red-500 text-red-600 dark:text-red-500 dark:border-red-500': activeTabModal === 'terlewat', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTabModal !== 'terlewat'}"
            class="whitespace-nowrap py-2 px-4 border-b-2 font-semibold text-sm transition-colors duration-150 flex items-center focus:outline-none">
            Sudah Terlewat
            @if(count($unfinishedTerlewatAsAnggota) > 0)
                <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs dark:bg-red-900 dark:text-red-200">{{ count($unfinishedTerlewatAsAnggota) }}</span>
            @endif
        </button>
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        <!-- Panel Revisi -->
        <div x-show="activeTabModal === 'revisi'" class="transition-opacity duration-200">
            @if($revisiCount > 0)
                <div class="mb-3 flex items-center gap-2 px-1">
                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <p class="text-xs text-orange-700 dark:text-orange-400 font-medium">Pengiriman berikut telah direvisi oleh Ketua Tim dan perlu dikirim ulang segera.</p>
                </div>
                <x-tables.dashboard-penugasan-anggota :penugasans="$revisiAsAnggota" />
            @else
                <div class="flex flex-col items-center justify-center py-10 gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tidak Ada Revisi</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-xs">Semua pengiriman saat ini tidak sedang dalam status revisi.</p>
                </div>
            @endif
        </div>

        <!-- Panel Sedang Berjalan -->
        <div x-show="activeTabModal === 'berjalan'" class="transition-opacity duration-200">
            @if(count($unfinishedBerjalanAsAnggota) > 0)
                <x-tables.dashboard-penugasan-anggota :penugasans="$unfinishedBerjalanAsAnggota" />
            @else
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400 text-sm">Tidak ada penugasan yang sedang berjalan.</div>
            @endif
        </div>

        <!-- Panel Sudah Terlewat -->
        <div x-show="activeTabModal === 'terlewat'" class="transition-opacity duration-200">
            @if(count($unfinishedTerlewatAsAnggota) > 0)
                <x-tables.dashboard-penugasan-anggota :penugasans="$unfinishedTerlewatAsAnggota" />
            @else
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-400 text-sm">Tidak ada penugasan yang sudah terlewat.</div>
            @endif
        </div>
    </div>
</div>
