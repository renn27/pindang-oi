<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanTransfer;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KegiatanTransferController extends Controller
{
    public function transfer(Request $request, Kegiatan $kegiatan)
    {
        $this->authorize('transfer', $kegiatan);

        $validated = $request->validate([
            'to_ketua_id' => [
                'required',
                Rule::exists('pegawais', 'id_pegawai')->where('is_active', true),
            ],
            'transferred_at' => ['required', 'date'],
        ], [
            'to_ketua_id.required' => 'Ketua Tim penerima wajib dipilih.',
            'to_ketua_id.exists' => 'Pegawai penerima tidak ditemukan atau tidak aktif.',
            'transferred_at.required' => 'Tanggal transfer wajib diisi.',
            'transferred_at.date' => 'Format tanggal transfer tidak valid.',
        ]);

        $newKetua = Pegawai::findOrFail($validated['to_ketua_id']);

        // Cegah transfer ke diri sendiri
        if ($newKetua->id_pegawai === $kegiatan->id_penanggung_jawab) {
            return back()->with('error', 'Tidak dapat mentransfer kegiatan ke penanggung jawab yang sama.');
        }

        // Cegah double transfer
        if ($kegiatan->isTransferred()) {
            return back()->with('error', 'Kegiatan ini sudah pernah ditransfer.');
        }

        DB::transaction(function () use ($kegiatan, $newKetua, $validated) {
            // Jika penerima belum memiliki role 'Ketua Tim', beri role tersebut
            $ketuaRole = Role::where('nama_role', 'Ketua Tim')->first();
            if ($ketuaRole && !$newKetua->hasRole('Ketua Tim')) {
                $newKetua->roles()->attach($ketuaRole->id);
            }

            // Atur active_role penerima menjadi 'Ketua Tim'
            if ($newKetua->active_role !== 'Ketua Tim') {
                $newKetua->update([
                    'active_role' => 'Ketua Tim',
                ]);
            }

            // Catat transfer
            KegiatanTransfer::create([
                'kegiatan_id' => $kegiatan->id_kegiatan,
                'from_ketua_id' => $kegiatan->id_penanggung_jawab,
                'to_ketua_id' => $newKetua->id_pegawai,
                'transferred_at' => $validated['transferred_at'],
            ]);

            // Update penanggung jawab di kegiatans
            $kegiatan->update([
                'id_penanggung_jawab' => $newKetua->id_pegawai,
            ]);
        });

        return back()->with('success', 'Kepemilikan kegiatan berhasil ditransfer kepada ' . $newKetua->nama_pegawai . '.');
    }
}
