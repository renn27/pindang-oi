@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Pengolahan SNLIK26" />

<!-- Bagian Tahun -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
  <!-- Tabel Utama -->
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
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
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
              Target
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
              Pengiriman
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">
              Penerimaan
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
          <!-- Sub-row 1 -->
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              1
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Penyiapan Peta
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              2450
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              950
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
              820
            </td>

          <!-- Sub-row 2 -->
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              2
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Entry Dokumen
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              15
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              7
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
              10
            </td>
          </tr>
          <!-- Sub-row 3 -->
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              2
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Scan Peta Lapangan
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              15
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              7
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
              10
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>



@endsection