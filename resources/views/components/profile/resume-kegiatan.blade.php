<div class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="shadow-default rounded-2xl bg-white px-5 px-5 dark:bg-gray-900 sm:px-6 sm:pt-6">
        <div class="flex justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Resume Kegiatan 2025
                </h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Detail performa kerja bulanan
                </p>
            </div>
            <!-- Dropdown Menu -->
            <x-common.dropdown-menu />
            <!-- End Dropdown Menu -->

        </div>
        <div class="relative h-[260px] flex items-center justify-center mt-5">
            {{-- Chart --}}
            <div id="chartResumeKegiatan" class="w-full h-full"></div>
        </div>
    </div>
</div>

