<?php

namespace App\Policies;

use App\Models\Kegiatan;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KegiatanPolicy
{
    /**
     * Hanya ketua tim PEMILIK kegiatan
     */
    protected function isOwner(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return $pegawai->hasRole('Ketua Tim')
            && $pegawai->id_pegawai === $kegiatan->id_penanggung_jawab;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        // Admin & Pimpinan bebas
        if (in_array($pegawai->active_role, ['Admin', 'Pimpinan'])) {
            return true;
        }

        $bidang = request()->route('bidang');
        if (! $bidang) {
            return false;
        }

        // KETUA TIM
        if ($pegawai->active_role === 'Ketua Tim') {
            return Kegiatan::where('id_bidang', $bidang->id_bidang)
                ->where('id_penanggung_jawab', $pegawai->id_pegawai)
                ->exists();
        }

        // ANGGOTA TIM
        if ($pegawai->active_role === 'Anggota Tim') {
            return Kegiatan::where('id_bidang', $bidang->id_bidang)
                ->whereHas('subKegiatans.penugasans.anggota', function ($q) use ($pegawai) {
                    $q->where('id_anggota', $pegawai->id_pegawai);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        if (in_array($pegawai->active_role, ['Admin', 'Pimpinan'])) {
            return true;
        }

        if ($pegawai->active_role === 'Ketua Tim') {
            return $kegiatan->id_penanggung_jawab === $pegawai->id_pegawai;
        }

        if ($pegawai->active_role === 'Anggota Tim') {
            return $kegiatan->subKegiatans()
                ->whereHas('penugasans.anggota', function ($q) use ($pegawai) {
                    $q->where('id_anggota', $pegawai->id_pegawai);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai): bool
    {
        return in_array($pegawai->active_role, [
            'Ketua Tim',
            'Pimpinan',
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return in_array($pegawai->active_role, ['Admin', 'Pimpinan'])
        || $kegiatan->id_penanggung_jawab === $pegawai->id_pegawai;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return in_array($pegawai->active_role, ['Admin', 'Pimpinan'])
        || $kegiatan->id_penanggung_jawab === $pegawai->id_pegawai;
    }

    /**
     * Determine whether the user can create sub kegiatan data.
     */
    public function createSubKegiatan(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        // return $this->isOwner($pegawai, $kegiatan);

        return in_array($pegawai->active_role, ['Admin', 'Pimpinan'])
        || $kegiatan->id_penanggung_jawab === $pegawai->id_pegawai;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return false;
    }
}
