<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use Illuminate\Auth\Access\Response;

class SubKegiatanPolicy
{
    /**
     * Hanya ketua tim PEMILIK kegiatan dan Sub Kegiatan di dalamnya
     */
    protected function canManageSubKegiatan(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return
            in_array($pegawai->active_role, ['Admin', 'Pimpinan'], true)
            || (
                $pegawai->active_role === 'Ketua Tim'
                && $pegawai->id_pegawai === $subKegiatan->kegiatan->id_penanggung_jawab
        );
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Pegawai $pegawai, SubKegiatan $SubKegiatan): bool
    {
        if (in_array($pegawai->active_role, ['Admin', 'Pimpinan'])) {
            return true;
        }

        if ($SubKegiatan->kegiatan->id_penanggung_jawab == $pegawai->id_pegawai) {
            return true;
        }

        return $SubKegiatan->penugasans()
            ->whereHas('penugasans.anggota', function ($q) use ($pegawai) {
                $q->where('id_anggota', $pegawai->id_pegawai);
            })->exists();
    }

    /**
     * Determine whether the user can create the model.
     */
    public function create(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return $this->canManageSubKegiatan($pegawai, $subKegiatan);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return $this->canManageSubKegiatan($pegawai, $subKegiatan);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return $this->canManageSubKegiatan($pegawai, $subKegiatan);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return false;
    }
}
