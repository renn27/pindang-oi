<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndikatorJPT extends Model
{
    use HasFactory;

    protected $table = 'indikator_jpts';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_rencana_jpt',
        'nama_indikator_jpt',
    ];

    public function rencanajpt() {
        return $this->belongsTo(RencanaJPT::class, 'id_rencana_jpt', 'id');
    }
}
