<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaPimpinan extends Model
{
    use SoftDeletes;

    protected $table = 'agenda_pimpinans';

    protected $fillable = [
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'id_rencana_jpt',
        'id_indikator_jpt',
        'link_bukti',
        'status',
    ];

    public function rencanaJpt()
    {
        return $this->belongsTo(RencanaJPT::class, 'id_rencana_jpt');
    }

    public function indikatorJpt()
    {
        return $this->belongsTo(IndikatorJPT::class, 'id_indikator_jpt');
    }
}