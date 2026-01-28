<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'pegawais';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'nama_pegawai
        ',
        'username',
        'password',
        'email',
        'alamat',
        'jabatan',
        'photo',
        'active_role',
    ];

    protected $hidden = [
        'password',
    ];

    public function isAnggota(): bool
    {
        return ! $this->hasAnyRole([
            'Admin',
            'Pimpinan',
            'Ketua Tim',
        ]);
    }

    // Cek apakah pegawai ini penanggung jawab kegiatan
    public function isKetuaOfKegiatan(Kegiatan $kegiatan): bool {
        return $kegiatan->id_penanggung_jawab === $this->id_pegawai;
    }

    // Cek apakah pegawai ini anggota penugasan pada sub kegiatan tertentu
    public function isAnggotaOfSubKegiatan(SubKegiatan $subKegiatan): bool {
        return $subKegiatan->penugasan
            ->pluck('id_pegawai')
            ->contains($this->id_pegawai);
    }

    public function modeKerja(): string
    {
        return $this->active_role === 'Ketua Tim'
            ? 'ketua'
            : 'anggota';
    }

    public function kegiatanYangDipimpin() {
        return $this->hasMany(Kegiatan::class, 'id_penanggung_jawab', 'id_pegawai');
    }

    public function penugasanSebagaiAnggota() {
        return $this->hasMany(Penugasan::class, 'id_anggota', 'id_pegawai');
    }

    public function penerimaanSebagaiPenerima() {
        return $this->hasMany(Penerimaan::class, 'id_penerima', 'id_pegawai');
    }

    public function kalenderDls() {
        return $this->hasMany(KalenderDL::class, 'id_pegawai', 'id_pegawai');
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'pegawai_role', 'pegawai_id', 'role_id');
    }

    public function hasRole(string $namaRole): bool
    {
        return $this->roles->contains('nama_role', $namaRole);
    }

    // ❗ TAMBAHAN (dibutuhkan oleh isAnggota)
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('nama_role')->intersect($roles)->isNotEmpty();
    }

    public function isActiveRole(string $namaRole): bool
    {
        return $this->active_role === $namaRole;
    }

    public function getActiveRole(): ?string
    {
        return $this->active_role;
    }

    public function getAuthIdentifierName() {
        return 'id_pegawai';
    }

    public function getAuthIdentifier() {
        return $this->{$this->getAuthIdentifierName()};
    }
}
