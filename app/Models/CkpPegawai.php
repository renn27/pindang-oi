<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CkpPegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ckp_pegawais';
    protected $primaryKey = 'id_ckp';
    public $incrementing = false;

    protected $fillable = [
        'id_pegawai',
        'id_penugasan',
        'id_sub_kegiatan',  
        'uraian',
        'jenis_ckp',
        'satuan',
        'target_kuantitas',
        'kode_butir_kegiatan',
        'angka_kredit',
        'keterangan',
        'realisasi',
        'persentase_realisasi',
        'tingkat_kualitas',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'id_penugasan', 'id_penugasan');
    }
}
