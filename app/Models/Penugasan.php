<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Penugasan extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'penugasans';
    protected $primaryKey = 'id_penugasan';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id_anggota',
        'id_sub_kegiatan',
        'id_jenis_kegiatan',
        'target',
        'satuan_target',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function anggota() { // pegawai diganti sebagai anggota supaya lebih jelas
        return $this->belongsTo(Pegawai::class, 'id_anggota', 'id_pegawai');
    }

    public function subKegiatan() {
        return $this->belongsTo(SubKegiatan::class, 'id_sub_kegiatan', 'id_sub_kegiatan');
    }

    public function jenisKegiatan() {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan');
    }

    public function pengirimans() {
        return $this->hasMany(Pengiriman::class, 'id_penugasan', 'id_penugasan');
    }

    public function latestPengiriman() {
        return $this->hasOne(Pengiriman::class, 'id_penugasan', 'id_penugasan')
            ->latestOfMany('tanggal_pengiriman');
    }

    public function latestPenerimaan() {
        return $this->hasOneThrough(
            Penerimaan::class,   // model akhir
            Pengiriman::class,   // model perantara
            'id_penugasan',      // FK di Pengiriman ke Penugasan
            'id_pengiriman',     // FK di Penerimaan ke Pengiriman
            'id_penugasan',      // local key di Penugasan
            'id_pengiriman'      // local key di Pengiriman
        )->latestOfMany('tanggal_penerimaan');
    }

    public function isDinasLuar()
    {
        $specialTypes = [
            'Pengawasan',
            'Pendataan',
            'Supervisi',
            'Perjalanan Dinas',
        ];

        return in_array($this->jenis_kegiatan, $specialTypes);
    }



    public function bolehKirim(): bool
    {
        if (!$this->latestPenerimaan) {
            return false; // belum diperiksa
        }
        
        $today   = Carbon::today();
        $mulai   = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        return $today->between($mulai, $selesai);
    }

    public function tooltipPengiriman(): ?string
    {
        $today   = Carbon::today();
        $mulai   = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // ⚠️ BELUM DIPERIKSA KETUA TIM
        if (!$this->latestPenerimaan) {
            return 'warning|Pengiriman sedang diperiksa oleh ketua tim';
        }   

        // ❌ TELAT
        if ($today->gt($selesai)) {
            return 'danger|Penugasan telah berakhir · Anda terlambat / tidak mengirimkan penugasan';
        }

        // ⏳ BELUM MULAI
        if ($today->lt($mulai)) {
            $hari = $today->diffInDays($mulai);

            $text = $hari === 1
                ? 'Belum dimulai · Aktif 1 hari lagi'
                : 'Belum dimulai · Aktif '.$hari.' hari lagi';

            return 'info|'.$text;
        }

        return null; // aktif
    }

}
