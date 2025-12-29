<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengiriman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengirimans';
    protected $primaryKey = 'id_pengiriman';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id_penugasan',
        'tanggal_pengiriman',
        'jumlah_dikirim',
        'media_pengiriman',
        'bukti_dukung',
    ];

    public function penugasan() {
        return $this->belongsTo(Penugasan::class, 'id_penugasan', 'id_penugasan');
    }

    public function penerimaan() {
        return $this->hasOne(Penerimaan::class, 'id_pengiriman', 'id_pengiriman');
    }

    // Nanti
    // public function getBuktiDukungUrlAttribute() {
    //     if ($this->bukti_dukung) {
    //         return asset('storage/' . $this->bukti_dukung);
    //     }
    //     return null;
    // }
}
