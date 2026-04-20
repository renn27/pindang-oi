<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CkpPegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ckp_pegawais';
    protected $primaryKey = 'id_ckp';
    public $incrementing = false;

    protected $fillable = [
        'id_pegawai',
        'ckpable_type',
        'ckpable_id',
        'tipe_ckp',
        'uraian',
        'jenis_ckp',
        'satuan',
        'target_kuantitas',
        'realisasi',
        'persentase_realisasi',
        'tingkat_kualitas',
        'kode_butir_kegiatan',
        'angka_kredit',
        'keterangan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    // Polymorphic — menunjuk ke parent (Penugasan / SubKegiatan / AgendaPimpinan)
    public function ckpable(): MorphTo
    {
        return $this->morphTo();
    }
}
