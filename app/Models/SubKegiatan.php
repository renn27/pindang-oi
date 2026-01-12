<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubKegiatan extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'sub_kegiatans';
    protected $primaryKey = 'id_sub_kegiatan';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'nama_sub_kegiatan',
        'target',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kegiatan() {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }

    public function penugasans() {
        return $this->hasMany(Penugasan::class, 'id_sub_kegiatan', 'id_sub_kegiatan');
    }

    public function scopeForUser($query, $user)
    {
        if (in_array($user->active_role, ['Admin', 'Pimpinan'])) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {

            // Ketua Tim lewat parent Kegiatan
            $q->whereHas('kegiatan', function ($k) use ($user) {
                $k->where('id_penanggung_jawab', $user->id_pegawai);
            })

            // Anggota tim langsung di penugasan
            ->orWhereHas('penugasans.anggota', function ($anggota) use ($user) {
                $anggota->where('id_anggota', $user->id_pegawai);
            });
        });
    }

}
