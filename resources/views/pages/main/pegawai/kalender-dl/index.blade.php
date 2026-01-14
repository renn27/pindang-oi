@extends('layouts.dashboard')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-wide">
                Kalender Dinas Luar
            </h2>
            <p class="text-sm text-gray-500">
                {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
            </p>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('kalenderDL.index') }}" 
              class="flex items-end gap-3 bg-white p-3 rounded-lg border shadow-sm">

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan</label>
                <select name="month" class="border rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun</label>
                <select name="year" class="border rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                    @for($y = now()->year - 3; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <button type="submit"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-md text-sm font-semibold transition shadow">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- Kalender --}}
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="border-b px-4 py-3 text-left font-semibold text-gray-700 
                                sticky left-0 bg-gray-50 z-30 w-[220px] min-w-[220px]">
                            Pegawai
                        </th>

                        <th class="border-b px-3 py-3 text-center font-semibold text-gray-700 
                                sticky left-[220px] bg-gray-50 z-30 w-[90px] min-w-[90px]">
                            Total DL
                        </th>

                        {{-- Tanggal --}}
                        @foreach($dates as $date)
                            <th class="border-b px-2 py-3 text-center w-12">
                                <div class="text-xs font-semibold text-gray-800">
                                    {{ $date->format('d') }}
                                </div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-wide">
                                    {{ $date->translatedFormat('D') }}
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach($pegawais as $index => $pegawai)

                        <tr class="hover:bg-blue-50 transition">
                            {{-- Nama --}}
                            <td class="border-b px-4 py-2 sticky left-0 bg-white z-20 
                                    whitespace-nowrap font-medium text-gray-800 w-[220px] min-w-[220px]">
                                {{ $pegawai->nama_pegawai }}
                            </td>


                            {{-- Total --}}
                            <td class="border-b px-3 py-2 text-center sticky left-[220px] bg-white z-20 w-[90px] min-w-[90px]">
                                @if($pegawai->total_dl_bulan_ini > 0)
                                    <div class="inline-flex items-center justify-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold shadow-sm">
                                        {{ $pegawai->total_dl_bulan_ini }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center justify-center bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-semibold">
                                        0
                                    </div>
                                @endif
                            </td>


                            {{-- Grid --}}
                            @foreach($dates as $date)
                                @php
                                    $hasDL = $pegawai->kalenderDLs->contains(function ($dl) use ($date) {
                                        return $dl->tanggal_dl === $date->toDateString();
                                    });
                                @endphp

                                <td class="border-b px-1 py-1 text-center">
                                    @if($hasDL)
                                        <div class="mx-auto h-6 w-6 rounded-md bg-blue-700 shadow-sm"
                                            title="Dinas Luar"></div>
                                    @else
                                        <div class="mx-auto h-6 w-6 rounded-md bg-gray-100"></div>
                                    @endif
                                </td>
                            @endforeach

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
        <div class="flex items-center gap-2">
            <div class="h-4 w-4 rounded bg-blue-700"></div>
            <span>Dinas Luar</span>
        </div>
    </div>

</div>
@endsection
