<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\JenisKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenugasanController extends Controller
{
    private array $specialTypes = ['Pengawasan', 'Pendataan', 'Supervisi', 'Perjalanan Dinas'];

    public function store(Request $request, SubKegiatan $subKegiatan)
    {
        $this->authorize('create', [Penugasan::class, $subKegiatan]);

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai'],
            'id_jenis_kegiatan' => ['required'],
            'jenis_kegiatan_baru' => ['nullable', 'string', 'max:100'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string', 'max:50'],

            'tanggal_pelaksanaan' => ['nullable', 'date', 'after_or_equal:today'],

            'tanggal_mulai' => ['nullable', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        /**
         * 🔥 HANDLE JENIS KEGIATAN (LAINNYA / EXISTING)
         */
        if ($validated['id_jenis_kegiatan'] === 'LAINNYA') {
            if (empty($validated['jenis_kegiatan_baru'])) {
                return back()->withErrors([
                    'jenis_kegiatan_baru' => 'Jenis kegiatan wajib diisi'
                ])->withInput();
            }

            $jenis = JenisKegiatan::create([
                'jenis_kegiatan' => $validated['jenis_kegiatan_baru'],
                'kategori' => 'Tambahan',
            ]);

            $validated['id_jenis_kegiatan'] = $jenis->id;
        } else {
            if (!JenisKegiatan::where('id', $validated['id_jenis_kegiatan'])->exists()) {
                abort(422, 'Jenis kegiatan tidak valid');
            }
        }

        unset($validated['jenis_kegiatan_baru']);

        /**
         * 🔥 TENTUKAN JENIS KEGIATAN KHUSUS / BUKAN
         */
        $jenisKegiatan = JenisKegiatan::find($validated['id_jenis_kegiatan']);
        $isSpecial = $this->isSpecialJenisKegiatan($jenisKegiatan?->jenis_kegiatan);

        /**
         * 🔥 MAPPING TANGGAL (INI INTI REFACTOR)
         */
        if ($isSpecial) {
            if (empty($validated['tanggal_pelaksanaan'])) {
                return back()->withErrors([
                    'tanggal_pelaksanaan' => 'Tanggal pelaksanaan wajib diisi untuk jenis kegiatan ini'
                ])->withInput();
            }

            $validated['tanggal_mulai']   = $validated['tanggal_pelaksanaan'];
            $validated['tanggal_selesai'] = $validated['tanggal_pelaksanaan'];
        } else {
            if (empty($validated['tanggal_mulai']) || empty($validated['tanggal_selesai'])) {
                return back()->withErrors([
                    'tanggal_mulai' => 'Tanggal mulai wajib diisi',
                    'tanggal_selesai' => 'Tanggal selesai wajib diisi',
                ])->withInput();
            }
        }

        unset($validated['tanggal_pelaksanaan']);

        // STATUS AWAL
        $validated['status'] = 'Belum Dikirim';

        try {
            $subKegiatan->penugasans()->create($validated);

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Penugasan kepada anggota berhasil dilakukan.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membuat penugasan kepada anggota. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function update(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan)
    {
        // dd($request->all());
        $this->authorize('update', $penugasan);

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai'],
            'id_jenis_kegiatan' => ['required'],
            'jenis_kegiatan_baru' => ['nullable', 'string', 'max:100'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string', 'max:50'],

            'tanggal_pelaksanaan' => ['nullable', 'date', 'after_or_equal:today'],

            'tanggal_mulai' => ['nullable', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        /**
         * 🔥 DETEKSI JENIS KEGIATAN
         */
        $jenisKegiatan = JenisKegiatan::find($validated['id_jenis_kegiatan']);
        $isSpecial = $this->isSpecialJenisKegiatan($jenisKegiatan?->jenis_kegiatan);

        /**
         * 🔥 MAPPING TANGGAL
         */
        if ($isSpecial) {
            if (empty($validated['tanggal_pelaksanaan'])) {
                return back()->withErrors([
                    'tanggal_pelaksanaan' => 'Tanggal pelaksanaan wajib diisi untuk jenis kegiatan ini'
                ])->withInput();
            }

            $validated['tanggal_mulai']   = $validated['tanggal_pelaksanaan'];
            $validated['tanggal_selesai'] = $validated['tanggal_pelaksanaan'];
        } else {
            if (empty($validated['tanggal_mulai']) || empty($validated['tanggal_selesai'])) {
                return back()->withErrors([
                    'tanggal_mulai' => 'Tanggal mulai wajib diisi',
                    'tanggal_selesai' => 'Tanggal selesai wajib diisi',
                ])->withInput();
            }
        }

        unset($validated['tanggal_pelaksanaan']);

        try {
            $penugasan->update($validated);

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Data Penugasan kepada anggota berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Update penugasan gagal', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan sistem.')
                ->withInput();
        }
        // } catch (\Exception $e) {
        //     dd($e->getMessage());

        //     return redirect()->back()
        //         ->with('error', 'Gagal memperbarui data penugasan kepada anggota. Silakan coba lagi.')
        //         ->withInput();
        // }
    }

    /**
     * 🔥 HELPER: cek jenis kegiatan khusus
     */
    private function isSpecialJenisKegiatan(?string $namaJenis): bool
    {
        if (!$namaJenis) return false;

        foreach ($this->specialTypes as $type) {
            if (stripos($namaJenis, $type) !== false) {
                return true;
            }
        }

        return false;
    }

    public function delete() {}
}
