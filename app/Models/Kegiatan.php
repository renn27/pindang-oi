<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'kegiatans';
    protected $primaryKey = 'id_kegiatan';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id_bidang',
        'id_penanggung_jawab',
        'tahun_kegiatan',
        'rk_jpt',
        'iki_jpt',
        'nama_rk_kegiatan',
    ];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang', 'id_bidang');
    }

    public function rencanaJpt()
    {
        return $this->belongsTo(RencanaJpt::class, 'rk_jpt', 'id');
    }

    public function indikatorJpt()
    {
        return $this->belongsTo(IndikatorJPT::class, 'iki_jpt', 'id'); // kolom Kegiatan yang nyimpen id indikator
    }

    public function penanggungJawab() { // pegawai diganti sebagai penanggung jawab supaya lebih jelas
        return $this->belongsTo(Pegawai::class, 'id_penanggung_jawab', 'id_pegawai');
    }

    public function subKegiatans() {
        return $this->hasMany(SubKegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
