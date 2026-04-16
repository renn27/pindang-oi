@props(['penugasans'])

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 dark:text-gray-400">No.</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">Bidang</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Penugasan</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Anggota</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">Target</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">Deadline</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40 dark:text-gray-400">Status Penugasannya</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @foreach ($penugasans as $index => $penugasan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 cursor-pointer"
                        onclick="window.location.href='{{ route('sub.kegiatan.show', ['kegiatan' => $penugasan->subKegiatan->id_kegiatan, 'subKegiatan' => $penugasan->id_sub_kegiatan]) }}#penugasan-{{ $penugasan->id_penugasan }}'">
                        <td class="px-4 py-4 text-sm text-gray-600 font-medium text-center dark:text-gray-400">
                            {{ $penugasans->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">
                                {{ $penugasan->subKegiatan->kegiatan->bidang->nama_bidang ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 p-0 w-80 md:w-full break-words">
                                    {{ $penugasan->subKegiatan->nama_sub_kegiatan ?? '-' }}
                                </span>
                                <span class="text-xs text-brand-600 font-medium dark:text-brand-400 line-clamp-2">
                                    Jenis: {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex py-1 px-3 rounded-md bg-gray-100 text-gray-800 text-xs font-bold dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                {{ $penugasan->target }} {{ $penugasan->satuan_target }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $deadlineDate = \Carbon\Carbon::parse($penugasan->tanggal_selesai)->endOfDay();
                                $todayDate = now()->startOfDay();
                                $diffInDays = (int) $todayDate->diffInDays($deadlineDate->copy()->startOfDay(), false);
                                
                                $countdownText = '';
                                $countdownClass = '';
                                
                                if ($diffInDays > 0) {
                                    $countdownText = $diffInDays . ' hari lagi';
                                    $countdownClass = $diffInDays <= 3 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700';
                                } elseif ($diffInDays === 0) {
                                    $countdownText = 'Hari ini';
                                    $countdownClass = 'bg-orange-100 text-orange-700 font-bold';
                                } else {
                                    $countdownText = 'Terlewat ' . abs($diffInDays) . ' hari';
                                    $countdownClass = 'bg-red-200 text-red-800 font-bold';
                                }
                            @endphp
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-gray-500 font-medium dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($penugasan->tanggal_selesai)->format('d M Y') }}
                                </span>
                                <span class="inline-flex w-fit px-2 py-1 text-[10px] font-bold rounded {{ $countdownClass }}">
                                    {{ $countdownText }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $statusInfo = $penugasan->statusPenugasan();
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusInfo['class'] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusInfo['label'] ?? 'Tidak Diketahui' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($penugasans->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        {{ $penugasans->links() }}
    </div>
    @endif
</div>
