<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawais';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'nama_pegawai',
        'username',
        'password',
        'email',
        'alamat',
        'jabatan',
        'photo',
    ];

    protected $hidden = [
        'password',
    ];


    
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

    public function kegiatanYangDipimpin() {
        return $this->hasMany(Kegiatan::class, 'id_penanggung_jawab', 'id_pegawai');
    }

    public function penugasanSebagaiAnggota() {
        return $this->hasMany(Penugasan::class, 'id_anggota', 'id_pegawai');
    }

    public function penerimaanSebagaiPenerima() {
        return $this->hasMany(Penerimaan::class, 'id_penerima', 'id_pegawai');
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'pegawai_role', 'pegawai_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('nama_role', $roleName)->exists();
    }

    public function hasAnyRole(array $roleNames) {
        return $this->roles()->whereIn('nama_role', $roleNames)->exists();
    }

    public function getAuthIdentifierName() {
        return 'id_pegawai';
    }

    public function getAuthIdentifier() {
        return $this->{$this->getAuthIdentifierName()};
    }
}
