<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Pegawai extends Authenticatable
{
    use HasFactory, HasPushSubscriptions, Notifiable, SoftDeletes, HasUuids;

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
        'active_role',
    ];

    protected $hidden = [
        'password',
    ];

    public function isSuperUser(): bool
    {
        return $this->isActiveRole('Admin') || $this->isActiveRole('Pimpinan');
    }

    public function isKetuaTim(): bool
    {
        return $this->isActiveRole('Ketua Tim');
    }

    public function isAnggotaTim(): bool
    {
        return
            $this->isActiveRole('Anggota Tim');
    }

    public function kegiatanYangDipimpin()
    {
        return $this->hasMany(Kegiatan::class, 'id_penanggung_jawab', 'id_pegawai');
    }

    public function penugasanSebagaiAnggota()
    {
        return $this->hasMany(Penugasan::class, 'id_anggota', 'id_pegawai');
    }

    public function penerimaanSebagaiPenerima()
    {
        return $this->hasMany(Penerimaan::class, 'id_penerima', 'id_pegawai');
    }

    public function kalenderDls()
    {
        return $this->hasMany(KalenderDL::class, 'id_pegawai', 'id_pegawai');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'pegawai_role', 'pegawai_id', 'role_id');
    }

    public function hasRole(string $namaRole): bool
    {
        return $this->roles->contains('nama_role', $namaRole);
    }

    public function getDisplayRoleAttribute(): string
    {
        // 1️⃣ Kalau active role ada
        if (!empty($this->active_role)) {
            return $this->active_role;
        }

        // 2️⃣ Kalau tidak ada active role tapi punya penugasan
        if (\App\Models\Penugasan::where('id_anggota', $this->id_pegawai)->exists()) {
            return 'Anggota Tim';
        }

        // 3️⃣ Default
        return 'Belum Ada Role';
    }

    // ❗ TAMBAHAN (dibutuhkan oleh isAnggota)
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('nama_role')->intersect($roles)->isNotEmpty();
    }

    public function isActiveRole(string $namaRole): bool
    {
        return $this->getActiveRole() === $namaRole;
    }

    public function getActiveRole(): ?string
    {
        return $this->active_role;
    }

    public function getAuthIdentifierName()
    {
        return 'id_pegawai';
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    // relasi ke tabel ckp
    public function ckps()
    {
        return $this->hasMany(CkpPegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
