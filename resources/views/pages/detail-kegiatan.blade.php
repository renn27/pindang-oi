@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Detail Kegiatan" />

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
  <!-- Header Kegiatan -->
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
      Penyiapan Peta SNLIK26
    </h1>
    
    <!-- Informasi Kegiatan dalam Tabel Format -->
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <tr>
            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 w-32">
              Kegiatan
            </td>
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              Penyiapan Peta SNLIK26
            </td>
          </tr>
          <tr>
            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
              Unit Kerja
            </td>
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              STATISTIK WARGA
            </td>
          </tr>
          <tr>
            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
              Jangka Kegiatan
            </td>
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              Bulanan
            </td>
          </tr>
          <tr>
            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
              Tanggal Mulai
            </td>
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              Wed, 01 Jan 2025
            </td>
          </tr>
          <tr>
            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
              Tanggal Berakhir
            </td>
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              Fri, 10 Jan 2025
            </td>
          </tr>
          <tr>
            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
              Satuan Kegiatan
            </td>
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              Peta
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Section Progres -->
  <div class="mb-8">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Progres</h2>
    
    <!-- Section Tracking Pengiriman -->
    <div class="mb-6">
      <h3 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Tracking Pengiriman</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
          <div class="flex items-center mb-2">
            <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Response Rate</span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400">Rasio kuantitas dokumen/dokumen yang dikumpulkan</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
          <div class="flex items-center mb-2">
            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ketepatan Waktu</span>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400">Tingkat ketepatan waktu pengumpulan dokumen</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabel Detail Pengiriman -->
  <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-800/50">
            <th rowspan="2" scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">
              No.
            </th>
            <th rowspan="2" scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Unit Kerja
            </th>
            <th rowspan="2" scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
              Target
            </th>
            <th colspan="3" scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l border-gray-200 dark:border-gray-700">
              Pengiriman
            </th>
            <th colspan="5" scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l border-gray-200 dark:border-gray-700">
              Penerimaan
            </th>
          </tr>
          <tr class="bg-gray-50 dark:bg-gray-800/50">
            <!-- Sub-headers for Pengiriman -->
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Detail
            </th>
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
              RR (%)
            </th>
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32 border-l border-gray-200 dark:border-gray-700">
              Ketepatan Waktu
            </th>
            <!-- Sub-headers for Penerimaan -->
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l border-gray-200 dark:border-gray-700">
              Detail
            </th>
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
              RR (%)
            </th>
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
              Ketepatan Waktu
            </th>
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
              Bukti Dukung Kegiatan
            </th>
            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
              Jadikan CKP
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
          <!-- Row 1 -->
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              1
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Ifone Arma
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              302
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Thu, 09 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 302 (webentry)
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center border-l border-gray-200 dark:border-gray-700">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Fri, 10 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 302
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              https://drive.com
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              <button @click="$dispatch('open-profile-address-modal')"
            class="flex items-center gap-2 rounded-full border border-gray-300
               bg-white px-4 py-3 text-sm font-medium text-gray-700
               shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
               dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
               dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
            <!-- icon -->
            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                    fill="" />
            </svg>
            Jadikan CKP
        </button>
            </td>

            
          </tr>
          
          <!-- Row 2 -->
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              2
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Leony Fransiska
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              396
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Thu, 09 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 396 (Webentry)
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center border-l border-gray-200 dark:border-gray-700">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Fri, 10 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 396
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
          </tr>
          
          <!-- Row 3 -->
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              3
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Rendi Alamsyah
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              857
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Thu, 09 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 857 (webentry)
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center border-l border-gray-200 dark:border-gray-700">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Fri, 10 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 857
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
          </tr>
          
          <!-- Row 4 -->
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
              4
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              Gusti Arya
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              696
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Thu, 09 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 696 ()
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center border-l border-gray-200 dark:border-gray-700">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">
              <div class="space-y-1">
                <div class="flex items-center">
                  <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                  <span>Fri, 10 Jan 2025</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                  Jumlah: 696
                </div>
              </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              100%
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
              <div class="flex justify-center">
                @for($i = 0; $i < 5; $i++)
                  <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
            </td>
          </tr>
          
          <!-- Row Total -->
          <tr class="bg-gray-50 dark:bg-gray-800 font-semibold">
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center" colspan="2">
              Total
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center">
              2251
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center">
              2251
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center">
              100%
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center border-l border-gray-200 dark:border-gray-700">
              100%
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center border-l border-gray-200 dark:border-gray-700">
              2251
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center">
              100%
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-center">
              100%
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection