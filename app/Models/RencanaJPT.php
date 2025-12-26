<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RencanaJPT extends Model
{
    use HasFactory;

    protected $table = 'rencana_jpts';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'tahun',
        'nama_rencana_jpt',
    ];

    public function indikatorjpts() {
        return $this->hasMany(IndikatorJPT::class, 'id_rencana_jpt', 'id');
    }

}
