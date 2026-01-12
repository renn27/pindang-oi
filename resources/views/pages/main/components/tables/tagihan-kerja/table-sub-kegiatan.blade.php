<!-- TABLE Sub Kegiatan -->
<div class="grid grid-cols-1">
    <div class="col-span-1 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col"
                        class="pl-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                        No.
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Sub Kegiatan
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                        Jumlah Anggota
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                        Tanggal Mulai
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                        Tanggal Selesai
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($kegiatan->subKegiatans as $index => $subKegiatan)
                    <!-- Sub-row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td
                            class="pl-6 py-4 whitespace-nowrap  text-sm font-medium text-gray-900 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <a href="{{ route('sub.kegiatan.show', [
                                'kegiatan' => $kegiatan->id_kegiatan,
                                'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                            ]) }}"
                                title="Lihat detail sub kegiatan"
                                class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $subKegiatan->nama_sub_kegiatan }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                            {{ $subKegiatan->penugasans->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                            {{ $subKegiatan->tanggal_mulai->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                            {{ $subKegiatan->tanggal_selesai->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex gap-4 items-center">
                                @can('update', $subKegiatan)
                                    {{-- Edit --}}
                                    <button class="flex flex-col items-center gap-1 text-blue-600 hover:text-blue-800"
                                        @click="$dispatch('open-smart-modal', {
                                        modalId: 'modal-sub-kegiatan',
                                        mode: 'edit',
                                        key: '{{ $subKegiatan->id_sub_kegiatan }}',
                                        data: {
                                            id_kegiatan: @js($kegiatan->id_kegiatan),
                                            nama_rk_kegiatan: @js($kegiatan->nama_rk_kegiatan),
                                            id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                            nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                            jenis_kegiatan: @js($subKegiatan->jenis_kegiatan),
                                            satuan_target: @js($subKegiatan->satuan_target),
                                            tanggal_mulai: @js($subKegiatan->tanggal_mulai),
                                            tanggal_selesai: @js($subKegiatan->tanggal_selesai),
                                            status: @js($subKegiatan->status),
                                        }
                                    })">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="text-xs font-medium">Edit</span>
                                    </button>
                                @endcan

                                @can('delete', $subKegiatan)
                                    {{-- Delete --}}
                                    <form id="delete-sub-kegiatan-{{ $subKegiatan->id_sub_kegiatan }}"
                                        action="{{ route('sub.kegiatan.delete', [
                                            'kegiatan' => $kegiatan->id_kegiatan,
                                            'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                                        ]) }}"
                                        method="POST" class="flex flex-col items-center">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button"
                                                onclick="SwalHelper.confirmDelete(
                                            'delete-sub-kegiatan-{{ $subKegiatan->id_sub_kegiatan }}',
                                            '{{ $subKegiatan->nama_sub_kegiatan }}'
                                        )"
                                            class="flex flex-col items-center gap-1 text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="text-xs font-medium">Hapus</span>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Belum ada Sub Kegiatan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
