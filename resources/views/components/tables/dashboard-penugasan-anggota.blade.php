@props(['penugasans'])

<div class="rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/50">
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest w-12">No.</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest w-28">Bidang</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Penugasan</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest w-28">Target</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest w-32">Mulai</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest w-32">Deadline</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest w-40">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/70">
                @foreach ($penugasans as $index => $penugasan)
                    @php
                        $deadlineDate = \Carbon\Carbon::parse($penugasan->tanggal_selesai)->endOfDay();
                        $todayDate = now()->startOfDay();
                        $diffInDays = (int) $todayDate->diffInDays($deadlineDate->copy()->startOfDay(), false);

                        $isUrgent = $diffInDays >= 0 && $diffInDays <= 3;
                        $isOverdue = $diffInDays < 0;

                        if ($diffInDays > 3) {
                            $countdownText = $diffInDays . ' hari lagi';
                            $countdownClass = 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400';
                        } elseif ($diffInDays >= 0) {
                            $countdownText = $diffInDays === 0 ? 'Hari ini' : $diffInDays . ' hari lagi';
                            $countdownClass = 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400';
                        } else {
                            $countdownText = 'Terlewat ' . abs($diffInDays) . ' hari';
                            $countdownClass = 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400';
                        }
                    @endphp
                    <tr
                        class="group cursor-pointer transition-all duration-150
                            {{ $isOverdue ? 'bg-red-50/30 dark:bg-red-900/5 hover:bg-red-50/60 dark:hover:bg-red-900/10' : ($isUrgent ? 'bg-orange-50/30 dark:bg-orange-900/5 hover:bg-orange-50/60 dark:hover:bg-orange-900/10' : 'hover:bg-gray-50/80 dark:hover:bg-gray-800/40') }}"
                        onclick="window.location.href='{{ route('sub.kegiatan.show', ['kegiatan' => $penugasan->subKegiatan->id_kegiatan, 'subKegiatan' => $penugasan->id_sub_kegiatan]) }}#penugasan-{{ $penugasan->id_penugasan }}'">

                        {{-- Indikator urgensi kiri --}}
                        <td class="pl-0 pr-4 py-3.5">
                            <div class="flex items-center">
                                <div class="w-0.5 h-10 rounded-r-full mr-4 {{ $isOverdue ? 'bg-red-400 dark:bg-red-500' : ($isUrgent ? 'bg-orange-400 dark:bg-orange-500' : 'bg-transparent group-hover:bg-gray-200 dark:group-hover:bg-gray-600') }} transition-colors duration-150"></div>
                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500 tabular-nums">
                                    {{ method_exists($penugasans, 'firstItem') ? $penugasans->firstItem() + $index : $index + 1 }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                {{ $penugasan->subKegiatan->kegiatan->bidang->nama_bidang ?? '-' }}
                            </span>
                        </td>

                        <td class="px-4 py-3.5">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 line-clamp-2 w-80 md:w-full break-words leading-snug group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors duration-150">
                                    {{ $penugasan->subKegiatan->nama_sub_kegiatan ?? '-' }}
                                </span>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                                    {{ $penugasan->jenisKegiatan?->jenis_kegiatan ?? '(jenis kegiatan telah dihapus)' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-semibold border border-gray-200/60 dark:border-gray-700/60">
                                {{ $penugasan->target }} <span class="font-normal text-gray-400">{{ $penugasan->satuan_target }}</span>
                            </span>
                        </td>

                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                {{ $penugasan->tanggal_mulai ? \Carbon\Carbon::parse($penugasan->tanggal_mulai)->format('d M Y') : '-' }}
                            </span>
                        </td>

                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                    {{ \Carbon\Carbon::parse($penugasan->tanggal_selesai)->format('d M Y') }}
                                </span>
                                <span class="inline-flex w-fit items-center px-2 py-0.5 text-[10px] font-bold rounded-md {{ $countdownClass }}">
                                    {{ $countdownText }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @php $statusInfo = $penugasan->statusPenugasan(); @endphp
                            <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $statusInfo['class'] ?? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $statusInfo['label'] ?? 'Tidak Diketahui' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (method_exists($penugasans, 'hasPages') && $penugasans->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $penugasans->links() }}
        </div>
    @endif
</div>
