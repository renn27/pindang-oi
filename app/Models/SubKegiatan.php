<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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
        'satuan_target',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
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

    public function ckp(): MorphOne
    {
        return $this->morphOne(CkpPegawai::class, 'ckpable');
    }

    // Tambahkan ini untuk mencegah terjadinya penghapusan data penugasan yang sudah masuk ke CKP Ketua Tim
    protected static function booted(): void
    {
        static::deleting(function ($subKegiatan) {
            if ($subKegiatan->ckp()->exists()) {
                throw new \RuntimeException('Sub Kegiatan tidak bisa dihapus karena sudah memiliki CKP.');
            }
        });
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
