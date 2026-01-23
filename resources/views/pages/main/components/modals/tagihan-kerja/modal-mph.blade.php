<x-ui.smart-modal id="modal-mph" class="max-w-7xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-mph') return;
        open = true;
    ">
    
    <div class="relative flex h-[90vh] w-full max-w-[1200px] flex-col overflow-hidden rounded-3xl bg-white">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-2xl font-semibold text-gray-800">Matriks Peran Hasil (MPH)</h4>
                    <p class="mt-1 text-sm text-gray-500">MPH {{ $title }} - Tahun 2026</p>
                </div>
                
                <!-- Pencarian Pegawai -->
                <div class="w-64">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Cari pegawai..." 
                               class="w-full h-10 rounded-lg border border-gray-300 pl-10 pr-4 text-sm focus:border-brand-300 focus:ring-2 focus:ring-brand-500/10">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- BODY (SCROLL DI SINI) -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <!-- Informasi Table Horizontal Scroll -->
            <div class="mb-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-700">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Anda dapat menggeser tabel ini ke kanan untuk melihat kolom lengkap</span>
                </div>
            </div>

            <!-- Container Tabel dengan Horizontal Scroll (DITUTUP DENGAN BENAR) -->
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <!-- Tabel Matriks -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="sticky left-0 z-10 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                No
                            </th>
                            <th scope="col" class="sticky left-20 z-10 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[300px]">
                                Pegawai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[150px]">
                                Jabatan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[120px]">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[250px]">
                                Rencana Kinerja 1
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[250px]">
                                Rencana Kinerja 2
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[250px]">
                                Rencana Kinerja 3
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <!-- Contoh Data 1 -->
                        <tr class="hover:bg-gray-50">
                            <td class="sticky left-0 z-10 bg-white px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                1
                            </td>
                            <td class="sticky left-20 z-10 bg-white px-6 py-4 whitespace-nowrap text-sm text-gray-900 min-w-[300px]">
                                <div class="font-medium">[340017814] Sukendro Suryo Wiguno SST, M.Ec.Dev</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Kepala BPS Kabupaten/Kota
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                    Ketua
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                Persentase Publikasi/Laporan Statistik Kependudukan Dan Ketenagakerjaan Yang Berkualitas
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                Persentase Publikasi/Laporan Statistik Kesejahteraan Rakyat Yang Berkualitas
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                Persentase Publikasi Statistik Ketahanan Yang Berkualitas
                            </td>
                        </tr>
                        
                        <!-- Contoh Data 2 -->
                        <tr class="hover:bg-gray-50">
                            <td class="sticky left-0 z-10 bg-white px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                2
                            </td>
                            <td class="sticky left-20 z-10 bg-white px-6 py-4 whitespace-nowrap text-sm text-gray-900 min-w-[300px]">
                                <div class="font-medium">[340051021] Akhmad Riza SE, M.M.</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Kepala Subbagian Umum
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">
                                    Anggota
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 italic">
                                -
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 italic">
                                -
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 italic">
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- INI TAG PENUTUP YANG BENAR -->

            <!-- Daftar Kegiatan dan Sub-Kegiatan -->
            <div class="mt-8 space-y-8">
                <!-- Kegiatan 1 -->
                <div class="rounded-xl border border-gray-200 bg-white">
                    <!-- Header Kegiatan -->
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <span class="mr-2 rounded-full bg-brand-500 px-3 py-1 text-sm font-medium text-white">1</span>
                            Nama Kegiatan 1
                        </h3>
                    </div>
                    
                    <!-- Sub Kegiatan 1.1 -->
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h4 class="mb-4 text-md font-medium text-gray-700">
                            <span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">1</span>
                            Nama Sub Kegiatan 1
                        </h4>
                        
                        <!-- Daftar Anggota Sub Kegiatan 1 -->
                        <div class="space-y-3">
                            @foreach(range(1, 3) as $i)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex min-w-[200px] items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-gray-600">P{{ $i }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Nama Pegawai {{ $i }}</div>
                                            <div class="text-xs text-gray-500">Jabatan {{ $i }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Jenis Kegiatan:</span> Jenis Kegiatan {{ $i }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-700">Target Waktu</div>
                                        <div class="text-xs text-gray-500">12/12/2024</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Sub Kegiatan 1.2 -->
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h4 class="mb-4 text-md font-medium text-gray-700">
                            <span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">2</span>
                            Nama Sub Kegiatan 2
                        </h4>
                        
                        <!-- Daftar Anggota Sub Kegiatan 2 -->
                        <div class="space-y-3">
                            @foreach(range(1, 2) as $i)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex min-w-[200px] items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-gray-600">P{{ $i }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Nama Pegawai {{ $i }}</div>
                                            <div class="text-xs text-gray-500">Jabatan {{ $i }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Jenis Kegiatan:</span> Jenis Kegiatan {{ $i }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-700">Target Waktu</div>
                                        <div class="text-xs text-gray-500">15/12/2024</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Sub Kegiatan 1.3 -->
                    <div class="px-6 py-5">
                        <h4 class="mb-4 text-md font-medium text-gray-700">
                            <span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">3</span>
                            Nama Sub Kegiatan 3
                        </h4>
                        
                        <!-- Daftar Anggota Sub Kegiatan 3 -->
                        <div class="space-y-3">
                            @foreach(range(1, 3) as $i)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex min-w-[200px] items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-gray-600">P{{ $i }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Nama Pegawai {{ $i }}</div>
                                            <div class="text-xs text-gray-500">Jabatan {{ $i }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Jenis Kegiatan:</span> Jenis Kegiatan {{ $i }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-700">Target Waktu</div>
                                        <div class="text-xs text-gray-500">20/12/2024</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Kegiatan 2 -->
                <div class="rounded-xl border border-gray-200 bg-white">
                    <!-- Header Kegiatan -->
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <span class="mr-2 rounded-full bg-brand-500 px-3 py-1 text-sm font-medium text-white">2</span>
                            Nama Kegiatan 2
                        </h3>
                    </div>
                    
                    <!-- Sub Kegiatan 2.1 -->
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h4 class="mb-4 text-md font-medium text-gray-700">
                            <span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">1</span>
                            Nama Sub Kegiatan 1
                        </h4>
                        
                        <!-- Daftar Anggota Sub Kegiatan 1 -->
                        <div class="space-y-3">
                            @foreach(range(1, 2) as $i)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex min-w-[200px] items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-gray-600">P{{ $i+3 }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Nama Pegawai {{ $i+3 }}</div>
                                            <div class="text-xs text-gray-500">Jabatan {{ $i+3 }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Jenis Kegiatan:</span> Jenis Kegiatan {{ $i }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-700">Target Waktu</div>
                                        <div class="text-xs text-gray-500">10/01/2025</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Sub Kegiatan 2.2 -->
                    <div class="px-6 py-5">
                        <h4 class="mb-4 text-md font-medium text-gray-700">
                            <span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">2</span>
                            Nama Sub Kegiatan 2
                        </h4>
                        
                        <!-- Daftar Anggota Sub Kegiatan 2 -->
                        <div class="space-y-3">
                            @foreach(range(1, 1) as $i)
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex min-w-[200px] items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-gray-600">P{{ $i+5 }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-700">Nama Pegawai {{ $i+5 }}</div>
                                            <div class="text-xs text-gray-500">Jabatan {{ $i+5 }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Jenis Kegiatan:</span> Jenis Kegiatan {{ $i }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-700">Target Waktu</div>
                                        <div class="text-xs text-gray-500">25/01/2025</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button @click="open = false" type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">
                    Tutup
                </button>
                
                <button type="button"
                    class="flex w-full justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 sm:w-auto">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export ke Excel
                </button>
                
                <button type="button"
                    class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Matriks
                </button>
            </div>
        </div>
    </div>
</x-ui.smart-modal>