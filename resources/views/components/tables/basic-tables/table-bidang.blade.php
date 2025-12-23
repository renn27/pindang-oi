@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Bidang Kerja" />

<!-- Bagian Tahun -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
  <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
    <!-- Label -->
    <div class="flex items-center h-10">
      <label class="text-sm font-medium text-gray-700 dark:text-gray-400 whitespace-nowrap">
        Tampilkan Data Tahun
      </label>
    </div>
    
    <!-- Dropdown -->
    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
      <select
        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
        <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
          2025
        </option>
        <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
          2024
        </option>
        <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
          2023
        </option>
        <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
          2022
        </option>
      </select>
      <span
        class="pointer-events-none absolute top-1/2 right-3.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
        <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </span>
    </div>

    <!-- Tombol -->
    <button
      type="button"
      class="flex justify-center items-center rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 w-full sm:w-auto h-10 whitespace-nowrap">
      Tampilkan
    </button>
  </div>
</div>

<!-- Tabel STATISTIK HARGA KONSUMEN DAN HPB -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
  <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">STATISTIK HARGA KONSUMEN DAN HPB</h3>
  
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead>
        <tr class="bg-gray-50 dark:bg-gray-800/50">
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
            No.
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Registrasi
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
        @for($i = 1; $i <= 19; $i++)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
          <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
            {{ $i }}
          </td>
          <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
            @switch($i)
              @case(1) Survey Harga Konsumen (NK 2.1) @break
              @case(2) Survey Harga Konsumen (NK 2.2) @break
              @case(3) Survey Harga Konsumen (NK 3) @break
              @case(4) Survey Harga Konsumen (NK 4) @break
              @case(5) Survey Harga Konsumen (NK 5) @break
              @case(6) Survey Harga Perdagangan Besar (SWPB) @break
              @case(7) Survey Harga Konsumen (NK 1.1) @break
              @case(8) Survey Harga Konsumen (NK 1.2) @break
              @case(9) Survey Harga Perdagangan Eceran (KKT, YW) @break
              @case(10) Survey Harga Konsumen (NK 6) @break
              @case(11) Survey Harga Konsumen Bulanan (NK 6) @break
              @case(12) Survey Harga Konsumen Bulanan (NK 5) @break
              @case(13) Survey Harga Konsumen Bulanan (NK 4) @break
              @case(14) Survey Harga Konsumen Bulanan (NK 3) @break
              @case(15) Survey Harga Konsumen Bulanan (NK 2.2) @break
              @case(16) Survey Harga Konsumen Bulanan (NK 2.1) @break
              @case(17) Survey Harga Perdagangan Besar (SWPB) @break
              @case(18) Survey Harga Konsumen Dua Mingguan (NK 1.2) @break
              @case(19) Survey Harga Konsumen Mingguan (NK 1.1) @break
            @endswitch
          </td>
        </tr>
        @endfor
      </tbody>
    </table>
  </div>
</div>

<!-- Tabel STATISTIK HARGA PRODUSEN DAN KEUNGAN -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
  <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">STATISTIK HARGA PRODUSEN DAN KEUNGAN</h3>
  
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead>
        <tr class="bg-gray-50 dark:bg-gray-800/50">
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
            No.
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Tanggal Berakhir
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Target
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Pengiriman
          </th>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Penerimaan
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
        @php
          $dataProdusen = [
            ['tanggal' => '20 Jan 2016', 'target' => 5, 'pengiriman' => 5, 'penerimaan' => 5],
            ['tanggal' => '20 Jan 2016', 'target' => 5, 'pengiriman' => 5, 'penerimaan' => 5],
            ['tanggal' => '25 Jan 2016', 'target' => 5, 'pengiriman' => 5, 'penerimaan' => 5],
            ['tanggal' => '25 Jan 2016', 'target' => 180, 'pengiriman' => 180, 'penerimaan' => 180],
            ['tanggal' => '25 Jan 2016', 'target' => 60, 'pengiriman' => 60, 'penerimaan' => 60],
            ['tanggal' => '26 Jan 2016', 'target' => 205, 'pengiriman' => 205, 'penerimaan' => 205],
            ['tanggal' => '31 Jan 2016', 'target' => 20, 'pengiriman' => 20, 'penerimaan' => 20],
            ['tanggal' => '31 Jan 2016', 'target' => 10, 'pengiriman' => 10, 'penerimaan' => 10],
            ['tanggal' => '12 Feb 2016', 'target' => 150, 'pengiriman' => 150, 'penerimaan' => 150],
            ['tanggal' => '20 Feb 2016', 'target' => 66, 'pengiriman' => 66, 'penerimaan' => 66],
            ['tanggal' => '20 Feb 2016', 'target' => 66, 'pengiriman' => 0, 'penerimaan' => 'TIDAK DILAKUKAN OPERATOR PROVINSI'],
            ['tanggal' => '20 Feb 2016', 'target' => 60, 'pengiriman' => 0, 'penerimaan' => 'TIDAK DILAKUKAN OPERATOR PROVINSI'],
            ['tanggal' => '20 Feb 2016', 'target' => 180, 'pengiriman' => 0, 'penerimaan' => 'TIDAK DILAKUKAN OPERATOR PROVINSI'],
            ['tanggal' => '20 Feb 2016', 'target' => 5, 'pengiriman' => 5, 'penerimaan' => 5],
            ['tanggal' => '20 Feb 2016', 'target' => 5, 'pengiriman' => 5, 'penerimaan' => 5],
            ['tanggal' => '20 Feb 2016', 'target' => 5, 'pengiriman' => 5, 'penerimaan' => 5],
            ['tanggal' => '25 Feb 2016', 'target' => 205, 'pengiriman' => 205, 'penerimaan' => 48],
            ['tanggal' => '29 Feb 2016', 'target' => 10, 'pengiriman' => 10, 'penerimaan' => 10],
            ['tanggal' => '29 Feb 2016', 'target' => 20, 'pengiriman' => 20, 'penerimaan' => 20],
          ];
        @endphp
        
        @foreach($dataProdusen as $index => $item)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
          <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
            {{ $index + 1 }}
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400">
            {{ $item['tanggal'] }}
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
            {{ $item['target'] }}
          </td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
            {{ $item['pengiriman'] }}
          </td>
          <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
            {{ $item['penerimaan'] }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection