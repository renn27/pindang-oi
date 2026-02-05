@extends('layouts.dashboard')

@section('content')
    <div class="p-6">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white tracking-wide">
                    Kalender Dinas Luar
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
                </p>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ route('kalenderDL.index') }}"
                class="flex items-end gap-3 bg-white dark:bg-gray-800 p-3 rounded-lg border shadow-sm dark:border-gray-700">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Bulan</label>
                    <select name="month"
                        class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-600">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Tahun</label>
                    <select name="year"
                        class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-600">
                        <option value="{{ now()->year }}" selected>
                            {{ now()->year }}
                        </option>
                        {{-- @for ($y = now()->year - 3; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor --}}
                    </select>
                </div>

                <div>
                    <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold transition shadow">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        {{-- Kalender --}}
        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">

            {{--
            Mobile & Tablet  : boleh scroll horizontal
            Desktop (lg+)    : NO horizontal scroll
        --}}
            <div class="w-full overflow-x-auto lg:overflow-x-hidden">
                <table class="w-full border-collapse table-fixed text-[11px] lg:text-[12px] dark:divide-gray-700">

                    {{-- HEADER --}}
                    <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0 z-20">
                        <tr>
                            {{-- Pegawai --}}
                            <th
                                class="border-b dark:border-gray-700 px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-300 w-[180px]">
                                Pegawai
                            </th>

                            {{-- Total --}}
                            <th
                                class="border-b dark:border-gray-700 px-2 py-2 text-center font-semibold text-gray-700 dark:text-gray-300 w-[60px]">
                                Total
                            </th>

                            {{-- Tanggal --}}
                            @foreach ($dates as $date)
                                <th
                                    class="border-b dark:border-gray-700 px-0.5 py-2 text-center
                                w-[34px] lg:w-[26px]">
                                    <div
                                        class="font-semibold text-gray-800 dark:text-gray-300 leading-tight text-[11px] lg:text-[10px]">
                                        {{ $date->format('d') }}
                                    </div>
                                    <div
                                        class="text-gray-500 dark:text-gray-400 uppercase leading-tight text-[9px] lg:text-[8px]">
                                        {{ $date->translatedFormat('D') }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody>
                        @foreach ($pegawais as $pegawai)
                            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition h-[34px]">

                                {{-- Nama --}}
                                <td
                                    class="border-b dark:border-gray-700 px-3 py-1 font-medium text-gray-800 dark:text-gray-300 truncate">
                                    {{ $pegawai->nama_pegawai }}
                                </td>

                                {{-- Total --}}
                                <td class="border-b dark:border-gray-700 px-2 py-1 text-center">
                                    @if ($pegawai->total_dl_bulan_ini > 0)
                                        <span
                                            class="inline-flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 px-2 py-0.5 rounded-full text-[11px] font-semibold">
                                            {{ $pegawai->total_dl_bulan_ini }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full text-[11px] font-semibold">
                                            0
                                        </span>
                                    @endif
                                </td>

                                {{-- Grid --}}
                                @foreach ($dates as $date)
                                    @php
                                        $hasDL = $pegawai->kalenderDLs->contains(function ($dl) use ($date) {
                                            return $dl->tanggal_dl === $date->toDateString();
                                        });
                                    @endphp

                                    <td class="border-b dark:border-gray-700 p-0.5 text-center">
                                        @if ($hasDL)
                                            <div class="mx-auto h-4 w-4 lg:h-3.5 lg:w-3.5 rounded bg-blue-700"></div>
                                        @else
                                            <div
                                                class="mx-auto h-4 w-4 lg:h-3.5 lg:w-3.5 rounded bg-gray-100 dark:bg-gray-700">
                                            </div>
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
        <div class="mt-4 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-blue-700"></div>
                <span>Dinas Luar</span>
            </div>
        </div>

    </div>
@endsection
