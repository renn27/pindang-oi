<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Penugasan;
use Illuminate\Auth\Access\Response;

class PenugasanPolicy
{

    protected function canManagePenugasan(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            in_array($pegawai->active_role, ['Admin', 'Pimpinan'], true)
            || (
                $pegawai->active_role === 'Ketua Tim'
                && $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab
            );
    }

    protected function isAssignedAnggota(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $pegawai->active_role === 'Anggota Tim'
            && $pegawai->id_pegawai === $penugasan->id_anggota;
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
    public function view(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $this->canManagePenugasan($pegawai, $penugasan) ||
            $this->isAssignedAnggota($pegawai, $penugasan);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return
            in_array($pegawai->active_role, ['Admin', 'Pimpinan'], true)
            || (
                $pegawai->active_role === 'Ketua Tim'
                && $pegawai->id_pegawai === $subKegiatan->kegiatan->id_penanggung_jawab
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // 1️⃣ Tidak berhak kelola
        if (! $this->canManagePenugasan($pegawai, $penugasan)) {
            return false;
        }

        // 2️⃣ Sudah masuk kalender DL → TIDAK BOLEH EDIT
        if ($penugasan->sudahMasukKalenderDL()) {
            return false;
        }

        // 3️⃣ Sudah diterima
        if ($penugasan->latestPenerimaan?->status === 'Diterima') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // hanya yang boleh manage (ketua / admin / pimpinan)
        if (! $this->canManagePenugasan($pegawai, $penugasan)) {
            return false;
        }

        // kalau sudah ada pengiriman → tidak boleh hapus
        if ($penugasan->latestPengiriman) {
            return false;
        }

        // kalau sudah diterima → tidak boleh hapus
        if ($penugasan->latestPenerimaan?->status === 'Diterima') {
            return false;
        }

        return true;
    }

    // === PENGIRIMAN ===
    public function send(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        if (!$this->isAssignedAnggota($pegawai, $penugasan)) {
            return false;
        }

        if ($penugasan->latestPenerimaan?->status === 'Diterima') {
            return false;
        }

        // ✅ masih revisi / menunggu → boleh muncul
        return true;
    }

    // === PENERIMAAN ===
    public function receive(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        if (!$this->canManagePenugasan($pegawai, $penugasan)) {
            return false;
        }

        if ($penugasan->latestPenerimaan?->status === 'Diterima') {
            return false;
        }

        return true;
    }

    public function acceptDL(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // 1️⃣ Hanya pimpinan
        if ($pegawai->active_role !== 'Pimpinan') {
            return false;
        }

        // 2️⃣ Penugasan memang butuh DL
        // (baik karena wajib DL atau toggle manual)
        if (! $penugasan->butuh_dl) {
            return false;
        }

        return true;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
    }
}
