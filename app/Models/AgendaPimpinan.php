<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class AgendaPimpinan extends Model {

    use HasFactory, HasUuids;
    
    protected $table = 'agenda_pimpinans';
    protected $primaryKey = 'id_agenda';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'nama_agenda',
        'tanggal_mulai',
        'tanggal_selesai',
        'rk_jpt',
        'iki_jpt',
        'target',
        'satuan_target',
        'realisasi',
        'link_bukti',
        'status',
    ];

    public function rencanaJpt()
    {
        return $this->belongsTo(RencanaJPT::class, 'rk_jpt', 'id');
    }

    public function indikatorJpt()
    {
        return $this->belongsTo(IndikatorJPT::class, 'iki_jpt', 'id'); // kolom AgendaPimpinan yang nyimpen id indikator
    }

    public function ckpBulanan(): MorphMany
    {
        return $this->morphMany(CkpPegawai::class, 'ckpable');
    }

    public function ckp(): MorphOne
    {
        return $this->morphOne(CkpPegawai::class, 'ckpable');
    }

    // Tambahkan ini untuk mencegah terjadinya penghapusan data penugasan yang sudah masuk ke CKP Pimpinan
    protected static function booted(): void
    {
        static::deleting(function ($agenda) {
            if ($agenda->ckp()->exists()) {
                throw new \RuntimeException('Agenda tidak bisa dihapus karena sudah memiliki CKP.');
            }
        });
    }
}
