@props(['id' => 'calcModal'])

<div x-data="{ open: false, calcData: {} }"
    x-show="open"
    @open-calc-modal.window="calcData = $event.detail; open = true"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-[99999]"
    aria-labelledby="modal-title" role="dialog" aria-modal="true"
    x-cloak>

    <!-- Backdrop -->
    <div x-show="open"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto custom-scrollbar">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open" @click.away="open = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 flex flex-col max-h-[90vh]">

                <!-- Header -->
                <div class="flex-none flex items-center justify-between rounded-t-2xl border-b border-gray-200 px-5 py-4 dark:border-gray-700 bg-white dark:bg-gray-900 z-10">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Perhitungan Kinerja — <span class="text-blue-600 dark:text-blue-400" x-text="calcData.nama"></span>
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 custom-scrollbar bg-white dark:bg-gray-900 space-y-5">

                    <!-- Fallback: no breakdown -->
                    <template x-if="!calcData.breakdown">
                        <div class="space-y-3">
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50 text-sm space-y-3">
                                <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">RR Kirim (F3) × 25%</span><span class="font-mono" x-text="Number(calcData.rr_kirim).toFixed(2) + '% × 0.25 = ' + (calcData.rr_kirim * 0.25).toFixed(2)"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Rating % (F4) × 25%</span><span class="font-mono" x-text="Number(calcData.rating_persen).toFixed(2) + '% × 0.25 = ' + (calcData.rating_persen * 0.25).toFixed(2)"></span></div>
                                <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">Kecepatan (F2) × 25%</span><span class="font-mono" x-text="Number(calcData.skor_cepat).toFixed(2) + '% × 0.25 = ' + (calcData.skor_cepat * 0.25).toFixed(2)"></span></div>
                            </div>
                            <div class="rounded-xl border border-brand-200 bg-brand-50 p-4 dark:border-brand-800/50 dark:bg-brand-900/20 flex justify-between items-center">
                                <span class="font-bold text-brand-900 dark:text-brand-100">Total Rata-rata</span>
                                <span class="text-2xl font-black text-brand-700 dark:text-brand-300" x-text="calcData.rata_rata + '%'"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Full breakdown -->
                    <template x-if="calcData.breakdown">
                        <div class="space-y-4">

                            <!-- Skor Akhir Banner -->
                            <div class="rounded-xl border border-brand-200 bg-gradient-to-r from-brand-50 to-blue-50 p-4 dark:border-brand-800/40 dark:from-gray-800 dark:to-gray-800 flex flex-wrap gap-4 items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400">Skor Akhir (Base + Bonus Fairness)</p>
                                    <p class="text-3xl font-black text-brand-700 dark:text-brand-300 mt-0.5" x-text="calcData.breakdown.rata_rata_final + '%'"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 space-y-0.5">
                                        <span>Base: <span class="font-mono" x-text="calcData.breakdown.rata_rata_base + '%'"></span></span>
                                        <span class="mx-1">+</span>
                                        <span>Bonus: <span class="font-mono"
                                            :class="(calcData.breakdown.bonus_aktual ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                            x-text="((calcData.breakdown.bonus_aktual ?? 0) >= 0 ? '+' : '') + (calcData.breakdown.bonus_aktual ?? 0) + '%'"></span></span>
                                    </p>
                                    <div class="mt-2 text-xs border-t border-brand-200/50 dark:border-gray-800 pt-2 text-gray-600 dark:text-gray-300 space-y-1">
                                        <!-- Case 1a: ruang_ke_100 is smaller (or equal) -->
                                        <template x-if="calcData.breakdown.penentu_bonus === 'ruang_ke_100'">
                                            <div>
                                                <span class="text-amber-600 dark:text-amber-400 font-bold">Penentu Bonus:</span> Ruang ke 100% (terkecil)
                                                <br>
                                                <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400">Rumus: 100% - Base = 100% - <span x-text="calcData.breakdown.rata_rata_base"></span>% = <span x-text="calcData.breakdown.bonus_aktual"></span>%</span>
                                            </div>
                                        </template>
                                        
                                        <!-- Case 1b: base * (koef - 1) is smaller -->
                                        <template x-if="calcData.breakdown.penentu_bonus === 'beban_kerja'">
                                            <div>
                                                <span class="text-blue-600 dark:text-blue-400 font-bold">Penentu Bonus:</span> Bonus Beban Kerja (terkecil)
                                                <br>
                                                <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400">Rumus: Base × (Koef - 1.0) = <span x-text="calcData.breakdown.rata_rata_base"></span>% × (<span x-text="calcData.breakdown.koefisien_beban"></span> - 1.0) = <span x-text="calcData.breakdown.rata_rata_base"></span>% × <span x-text="Number((calcData.breakdown.koefisien_beban - 1.0).toFixed(4))"></span> = <span x-text="calcData.breakdown.bonus_aktual"></span>%</span>
                                            </div>
                                        </template>

                                        <!-- Case 2: Koef < 1.0 -->
                                        <template x-if="calcData.breakdown.penentu_bonus === 'penalty'">
                                            <div>
                                                <span class="text-red-600 dark:text-red-400 font-bold">Penentu Penalti:</span> Kurang Beban Kerja (Koef &lt; 1.0)
                                                <br>
                                                <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400">Rumus: Base × (Koef - 1.0) = <span x-text="calcData.breakdown.rata_rata_base"></span>% × (<span x-text="calcData.breakdown.koefisien_beban"></span> - 1.0) = <span x-text="calcData.breakdown.rata_rata_base"></span>% × <span x-text="Number((calcData.breakdown.koefisien_beban - 1.0).toFixed(4))"></span> = <span x-text="calcData.breakdown.bonus_aktual"></span>%</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="rounded-lg bg-white/70 dark:bg-gray-700/50 px-3 py-2 text-center border border-gray-100 dark:border-gray-700">
                                        <div class="text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Jml Penugasan</div>
                                        <div class="font-bold text-gray-800 dark:text-white" x-text="calcData.breakdown.total_penugasan_dia"></div>
                                    </div>
                                    <div class="rounded-lg bg-white/70 dark:bg-gray-700/50 px-3 py-2 text-center border border-gray-100 dark:border-gray-700">
                                        <div class="text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Target Pegawai</div>
                                        <div class="font-bold text-gray-800 dark:text-white" x-text="calcData.breakdown.target_pegawai"></div>
                                    </div>
                                    <div class="rounded-lg bg-white/70 dark:bg-gray-700/50 px-3 py-2 text-center border border-gray-100 dark:border-gray-700">
                                        <div class="text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Avg Target Bulan</div>
                                        <div class="font-bold text-gray-800 dark:text-white" x-text="calcData.breakdown.avg_target_bulan"></div>
                                    </div>
                                    <div class="rounded-lg bg-purple-50 dark:bg-purple-900/20 px-3 py-2 text-center border border-purple-200 dark:border-purple-800/40">
                                        <div class="text-purple-600 dark:text-purple-400 uppercase tracking-wider mb-0.5">Koefisien Beban</div>
                                        <div class="font-bold text-purple-700 dark:text-purple-300" x-text="calcData.breakdown.koefisien_beban + 'x'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- F1: Penyelesaian -->
                            <div class="rounded-xl border border-blue-200 bg-blue-50/50 dark:border-blue-800/30 dark:bg-blue-900/10 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-blue-800 dark:text-blue-300 flex items-center gap-2">
                                        <span class="rounded-full bg-blue-200 dark:bg-blue-800 w-6 h-6 flex items-center justify-center text-xs font-black">1</span>
                                        F1 — Penyelesaian <span class="text-xs font-normal text-blue-600 dark:text-blue-400">(bobot 25%)</span>
                                    </h4>
                                    <span class="text-lg font-black text-blue-700 dark:text-blue-300" x-text="calcData.breakdown.f1.nilai + '%'"></span>
                                </div>
                                <div class="text-xs space-y-1.5 font-mono text-gray-700 dark:text-gray-300 bg-white/60 dark:bg-gray-800/40 rounded-lg p-3">
                                    <div class="flex justify-between"><span class="text-gray-500">Progress Pelunasan Diterima</span><span x-text="calcData.breakdown.f1.progress_pelunasan"></span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Progress Cicilan Diterima</span><span x-text="calcData.breakdown.f1.progress_cicilan"></span></div>
                                    <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-1 mt-1"><span class="text-gray-500">b_efektif = pelunasan + (cicilan × 0.5)</span><span x-text="calcData.breakdown.f1.b_efektif"></span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">a = total_target pegawai</span><span x-text="calcData.breakdown.f1.a"></span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">c = sum_target semua pegawai</span><span x-text="calcData.breakdown.f1.c"></span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">d = max(0, c − (a − b_efektif))</span><span x-text="calcData.breakdown.f1.d"></span></div>
                                    <div class="flex justify-between border-t border-blue-200 dark:border-blue-800/40 pt-1 mt-1 font-bold text-blue-700 dark:text-blue-300">
                                        <span>F1 = (d/c) × (b_efektif/a) × 100</span>
                                        <span x-text="calcData.breakdown.f1.nilai + '%'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- F2: Kecepatan -->
                            <div class="rounded-xl border border-green-200 bg-green-50/50 dark:border-green-800/30 dark:bg-green-900/10 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-green-800 dark:text-green-300 flex items-center gap-2">
                                        <span class="rounded-full bg-green-200 dark:bg-green-800 w-6 h-6 flex items-center justify-center text-xs font-black">2</span>
                                        F2 — Kecepatan Kirim <span class="text-xs font-normal text-green-600 dark:text-green-400">(Pelunasan only, bobot 25%)</span>
                                    </h4>
                                    <span class="text-lg font-black text-green-700 dark:text-green-300" x-text="calcData.breakdown.f2.nilai + '%'"></span>
                                </div>
                                <template x-if="calcData.breakdown.f2.detail.length === 0">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic px-3">Tidak ada Pelunasan+Diterima di bulan ini → F2 = 0</p>
                                </template>
                                <template x-for="(d, i) in calcData.breakdown.f2.detail" :key="i">
                                    <div class="mb-2 bg-white/60 dark:bg-gray-800/40 rounded-lg p-3 text-xs space-y-1.5 font-mono text-gray-700 dark:text-gray-300">
                                        <div class="font-bold text-green-700 dark:text-green-400 not-italic font-sans text-xs" x-text="(i+1) + '. ' + d.nama_sub_kegiatan"></div>
                                        <div class="flex justify-between"><span class="text-gray-500">Periode penugasan</span><span x-text="(d.tanggal_mulai||'').substring(0,10) + ' → ' + (d.tanggal_selesai||'').substring(0,10)"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500">Tgl pengiriman</span><span x-text="(d.tanggal_pengiriman||'').substring(0,10)"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500">lama_rentang (hari)</span><span x-text="d.lama_rentang"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500">lama_pengiriman (hari)</span><span x-text="d.lama_pengiriman"></span></div>
                                        <div class="flex justify-between" :class="d.terlambat ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                            <span>Status</span><span x-text="d.terlambat ? 'Terlambat' : 'Tepat / Lebih awal'"></span>
                                        </div>
                                        <div class="flex justify-between border-t border-green-200 dark:border-green-800/40 pt-1 mt-1 font-bold text-green-700 dark:text-green-300">
                                            <span>Score F2</span><span x-text="d.score_f2 + '%'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- F3: RR Kirim -->
                            <div class="rounded-xl border border-amber-200 bg-amber-50/50 dark:border-amber-800/30 dark:bg-amber-900/10 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                                        <span class="rounded-full bg-amber-200 dark:bg-amber-800 w-6 h-6 flex items-center justify-center text-xs font-black">3</span>
                                        F3 — RR Kirim <span class="text-xs font-normal text-amber-600 dark:text-amber-400">(bobot parsial, bobot 25%)</span>
                                    </h4>
                                    <span class="text-lg font-black text-amber-700 dark:text-amber-300" x-text="calcData.breakdown.f3.nilai + '%'"></span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs text-left">
                                        <thead class="text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            <tr class="border-b border-amber-200 dark:border-amber-800/30">
                                                <th class="pb-1 font-semibold">Sub Kegiatan</th>
                                                <th class="pb-1 text-center font-semibold">Tipe</th>
                                                <th class="pb-1 text-center font-semibold">RR</th>
                                                <th class="pb-1 text-center font-semibold">Bobot</th>
                                                <th class="pb-1 text-center font-semibold">Kontribusi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-mono divide-y divide-amber-100 dark:divide-amber-800/20">
                                            <template x-for="(d, i) in calcData.breakdown.f3.detail" :key="i">
                                                <tr class="text-gray-700 dark:text-gray-300">
                                                    <td class="py-1 pr-2 max-w-[160px] truncate font-sans" x-text="d.nama_sub_kegiatan"></td>
                                                    <td class="py-1 text-center">
                                                        <span :class="d.tipe_pengiriman === 'Pelunasan' ? 'text-green-700 dark:text-green-400' : (d.tipe_pengiriman === 'Cicilan' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400')"
                                                              x-text="d.tipe_pengiriman ?? '—'"></span>
                                                    </td>
                                                    <td class="py-1 text-center" x-text="d.rr_kirim !== null ? d.rr_kirim + '%' : '—'"></td>
                                                    <td class="py-1 text-center" x-text="d.bobot_parsial"></td>
                                                    <td class="py-1 text-center font-bold text-amber-700 dark:text-amber-300" x-text="d.kontribusi_rr"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-xs font-mono text-amber-700 dark:text-amber-400 border-t border-amber-200 dark:border-amber-800/30 pt-2">
                                    F3 = SUM(kontribusi_rr) / <span x-text="calcData.breakdown.total_penugasan_dia"></span> penugasan = <strong x-text="calcData.breakdown.f3.nilai + '%'"></strong>
                                </div>
                            </div>

                            <!-- F4: Rating -->
                            <div class="rounded-xl border border-rose-200 bg-rose-50/50 dark:border-rose-800/30 dark:bg-rose-900/10 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2">
                                        <span class="rounded-full bg-rose-200 dark:bg-rose-800 w-6 h-6 flex items-center justify-center text-xs font-black">4</span>
                                        F4 — Rating Kirim <span class="text-xs font-normal text-rose-600 dark:text-rose-400">(bobot parsial, bobot 25%)</span>
                                    </h4>
                                    <span class="text-lg font-black text-rose-700 dark:text-rose-300" x-text="calcData.breakdown.f4.nilai + '%'"></span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs text-left">
                                        <thead class="text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            <tr class="border-b border-rose-200 dark:border-rose-800/30">
                                                <th class="pb-1 font-semibold">Sub Kegiatan</th>
                                                <th class="pb-1 text-center font-semibold">Tipe</th>
                                                <th class="pb-1 text-center font-semibold">Rating</th>
                                                <th class="pb-1 text-center font-semibold">Bobot</th>
                                                <th class="pb-1 text-center font-semibold">Kontribusi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-mono divide-y divide-rose-100 dark:divide-rose-800/20">
                                            <template x-for="(d, i) in calcData.breakdown.f4.detail" :key="i">
                                                <tr class="text-gray-700 dark:text-gray-300">
                                                    <td class="py-1 pr-2 max-w-[160px] truncate font-sans" x-text="d.nama_sub_kegiatan"></td>
                                                    <td class="py-1 text-center">
                                                        <span :class="d.tipe_pengiriman === 'Pelunasan' ? 'text-green-700 dark:text-green-400' : (d.tipe_pengiriman === 'Cicilan' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400')"
                                                              x-text="d.tipe_pengiriman ?? '—'"></span>
                                                    </td>
                                                    <td class="py-1 text-center" x-text="d.rating_kirim !== null ? d.rating_kirim + '⭐' : '—'"></td>
                                                    <td class="py-1 text-center" x-text="d.bobot_parsial"></td>
                                                    <td class="py-1 text-center font-bold text-rose-700 dark:text-rose-300" x-text="d.kontribusi_rating"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-xs font-mono text-rose-700 dark:text-rose-400 border-t border-rose-200 dark:border-rose-800/30 pt-2">
                                    F4 = SUM(rating×20×bobot) / <span x-text="calcData.breakdown.total_penugasan_dia"></span> penugasan = <strong x-text="calcData.breakdown.f4.nilai + '%'"></strong>
                                </div>
                            </div>

                        </div>
                    </template>

                </div>

                <!-- Footer -->
                <div class="flex-none flex justify-end gap-3 rounded-b-2xl border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <button @click="open = false" type="button"
                        class="inline-flex justify-center items-center rounded-xl px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
