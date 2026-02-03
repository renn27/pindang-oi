<div class="rounded-2xl border border-gray-200 p-5 lg:p-6 bg-white dark:border-gray-800 dark:bg-gray-900">

    @if ($bestEmployee)
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

            <div class="flex w-full flex-col items-center gap-6 xl:flex-row">

                {{-- Avatar --}}
                <div
                    class="h-20 w-20 overflow-hidden rounded-full border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800">
                    <img src="{{ $bestEmployee->photo ? asset('storage/' . $bestEmployee->photo) : asset('images/user/userlogodefault.png') }}"
                        class="h-full w-full object-cover" alt="Foto {{ $bestEmployee->nama_pegawai }}" />
                </div>

                {{-- Info --}}
                <div class="order-3 xl:order-2">
                    <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 xl:text-left dark:text-white">
                        {{ $bestEmployee->nama_pegawai }}
                    </h4>

                    <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left mb-4">
                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Peringkat 1 Tahun {{ now()->year }}
                        </p>

                        <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>

                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            {{ number_format($bestEmployee->rata_rata, 0) }} % Pencapaian
                        </p>
                    </div>

                    <span
                        class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                        🏆 Best Employee
                    </span>
                </div>

            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data ranking</p>
    @endif

</div>
