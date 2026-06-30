@props(['bestEmployee', 'showHeader' => false, 'title' => 'Best Employee'])

<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:p-6">
    @if ($showHeader)
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $title }}</h3>
            <div class="inline-flex w-fit items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-700 dark:border-amber-800/40 dark:bg-amber-900/20 dark:text-amber-300">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                </svg>
                <span x-text="months[selectedMonth - 1] + ' ' + selectedYear"></span>
            </div>
        </div>
    @endif

    @if ($bestEmployee)
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex w-full flex-col items-center gap-6 xl:flex-row">
                <div class="h-20 w-20 overflow-hidden rounded-full border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800">
                    <img src="{{ ($bestEmployee->photo ?? null) ? asset('storage/' . $bestEmployee->photo) : asset('images/user/userlogodefault.png') }}" class="h-full w-full object-cover" alt="Foto {{ $bestEmployee->nama_pegawai }}" />
                </div>

                <div class="order-3 xl:order-2">
                    <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 dark:text-white xl:text-left">
                        {{ $bestEmployee->nama_pegawai }}
                    </h4>

                    <div class="mb-4 flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Peringkat 1 Tahun {{ now()->year }}
                        </p>

                        <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>

                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            {{ number_format($bestEmployee->rata_rata, 0) }} % Pencapaian
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M5 3.5A1.5 1.5 0 0 1 6.5 2h7A1.5 1.5 0 0 1 15 3.5V4h1.25A1.75 1.75 0 0 1 18 5.75v.5A4.75 4.75 0 0 1 13.53 11 4.52 4.52 0 0 1 11 12.42V15h2a1 1 0 1 1 0 2H7a1 1 0 1 1 0-2h2v-2.58A4.52 4.52 0 0 1 6.47 11 4.75 4.75 0 0 1 2 6.25v-.5A1.75 1.75 0 0 1 3.75 4H5v-.5ZM5 6H3.75a.25.25 0 0 0-.25.25v.5a3.25 3.25 0 0 0 2.35 3.12A4.5 4.5 0 0 1 5 7.25V6Zm9.15 3.87A3.25 3.25 0 0 0 16.5 6.75v-.5A.25.25 0 0 0 16.25 6H15v1.25c0 .95-.3 1.83-.85 2.62Z"/>
                        </svg>
                        {{ $title }}
                    </span>
                </div>
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data ranking</p>
    @endif
</div>
