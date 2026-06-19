<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nama_pegawai',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActiveRole(): ?string
    {
        return $this->active_role ?? 'Pegawai';
    }

    public function isActiveRole(string $namaRole): bool
    {
        return $this->getActiveRole() === $namaRole;
    }

    public function getNamaPegawaiAttribute(): string
    {
        return $this->name;
    }

    public function setNamaPegawaiAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getUsernameAttribute(): string
    {
        return $this->name;
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getActiveRoleAttribute(): string
    {
        return 'Pegawai';
    }

    public function getDisplayRoleAttribute(): string
    {
        return 'Pegawai';
    }

    public function getIdPegawaiAttribute(): int
    {
        return $this->id;
    }

    public function getPhotoAttribute(): ?string
    {
        return null;
    }

    public function isSuperUser(): bool
    {
        return false;
    }

    public function isKetuaTim(): bool
    {
        return false;
    }

    public function isAnggotaTim(): bool
    {
        return false;
    }

    public function kegiatanYangDipimpin()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function penugasanSebagaiAnggota()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function penerimaanSebagaiPenerima()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function kalenderDls()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function ckps()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1 = 0');
    }

    public function roles()
    {
        return $this->hasMany(User::class, 'id', 'id')->whereRaw('1 = 0');
    }
}
