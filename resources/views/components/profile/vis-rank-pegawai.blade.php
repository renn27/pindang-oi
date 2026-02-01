<div class="bg-white rounded-2xl shadow p-6">
    <h2 class="text-xl font-bold mb-4">
        Penilaian Kinerja Pegawai
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr class="text-left text-gray-600">
                    <th class="px-4 py-2">Nama Pegawai</th>
                    <th class="px-4 py-2 text-center">RR Kirim (%)</th>
                    <th class="px-4 py-2 text-center">Rating ⭐</th>
                    <th class="px-4 py-2 text-center">Rating (%)</th>
                    <th class="px-4 py-2 text-center">Rata-rata</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach ($rankPegawai as $pegawai)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $pegawai->nama_pegawai }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ number_format($pegawai->rr_kirim, 0) }}%
                        </td>

                        <td class="px-4 py-3 text-center">
                            @php
                                $rating = round($pegawai->rating_kirim, 1);
                                $fullStar = floor($rating);
                                $halfStar = ($rating - $fullStar) >= 0.5;
                                $emptyStar = 5 - $fullStar - ($halfStar ? 1 : 0);
                            @endphp

                            {{ number_format($pegawai->rating_kirim) }}
                            <div class="flex justify-center items-center gap-1">
                                {{-- Full star --}}
                                @for ($i = 0; $i < $fullStar; $i++)
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966
                                        a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81
                                        l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966
                                        c.3.921-.755 1.688-1.54 1.118l-3.38-2.455
                                        a1 1 0 00-1.176 0l-3.38 2.455
                                        c-.784.57-1.838-.197-1.539-1.118l1.287-3.966
                                        a1 1 0 00-.364-1.118L2.05 9.393
                                        c-.783-.57-.38-1.81.588-1.81h4.173
                                        a1 1 0 00.95-.69l1.286-3.966z"/>
                                    </svg>
                                @endfor

                                {{-- Half star --}}
                                @if ($halfStar)
                                    <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                                        <defs>
                                            <linearGradient id="halfStar">
                                                <stop offset="50%" stop-color="currentColor"/>
                                                <stop offset="50%" stop-color="transparent"/>
                                            </linearGradient>
                                        </defs>
                                        <path fill="url(#halfStar)" stroke="currentColor" stroke-width="1"
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966
                                            a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81
                                            l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966
                                            c.3.921-.755 1.688-1.54 1.118l-3.38-2.455
                                            a1 1 0 00-1.176 0l-3.38 2.455
                                            c-.784.57-1.838-.197-1.539-1.118l1.287-3.966
                                            a1 1 0 00-.364-1.118L2.05 9.393
                                            c-.783-.57-.38-1.81.588-1.81h4.173
                                            a1 1 0 00.95-.69l1.286-3.966z"/>
                                    </svg>
                                @endif

                                {{-- Empty star --}}
                                @for ($i = 0; $i < $emptyStar; $i++)
                                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966
                                        a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81
                                        l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966
                                        c.3.921-.755 1.688-1.54 1.118l-3.38-2.455
                                        a1 1 0 00-1.176 0l-3.38 2.455
                                        c-.784.57-1.838-.197-1.539-1.118l1.287-3.966
                                        a1 1 0 00-.364-1.118L2.05 9.393
                                        c-.783-.57-.38-1.81.588-1.81h4.173
                                        a1 1 0 00.95-.69l1.286-3.966z"/>
                                    </svg>
                                @endfor
                            </div>
                        </td>


                        <td class="px-4 py-3 text-center">
                            {{ number_format($pegawai->rating_persen, 0) }}%
                        </td>

                        <td class="px-4 py-3 text-center font-semibold">
                            {{ number_format($pegawai->rata_rata, 0) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
