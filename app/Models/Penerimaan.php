<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penerimaan extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'penerimaans';
    protected $primaryKey = 'id_penerimaan';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id_pengiriman',
        'id_penerima',
        'tanggal_penerimaan',
        'jumlah_diterima',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_penerimaan'   => 'date',
    ];

    public function penerima() { // pegawai diganti sebagai penerima supaya lebih jelas
        return $this->belongsTo(Pegawai::class, 'id_penerima', 'id_pegawai');
    }

    public function pengiriman() {
        return $this->belongsTo(Pengiriman::class, 'id_pengiriman', 'id_pengiriman');
    }
}
