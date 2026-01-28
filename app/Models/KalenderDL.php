<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalenderDL extends Model
{
    use HasFactory;

    protected $table = 'kalenderdls';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id_pegawai',
        'id_penugasan',
        'tanggal_dl',
        'keterangan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'id_penugasan');
    }
}
