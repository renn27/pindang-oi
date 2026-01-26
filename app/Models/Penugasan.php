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
        $today   = Carbon::today();
        $mulai   = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // belum masuk waktu
        if (!$today->between($mulai, $selesai)) {
            return false;
        }

        // SUDAH KIRIM tapi BELUM DIPERIKSA → tidak boleh kirim lagi
        if ($this->latestPengiriman && !$this->latestPenerimaan) {
            return false;
        }

        // BELUM PERNAH KIRIM → boleh kirim
        return true;
    }


    public function tooltipPengiriman(): ?string
    {
        $today   = Carbon::today();
        $mulai   = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // ⏳ BELUM MULAI
        if ($today->lt($mulai)) {
            $hari = $today->diffInDays($mulai);

            return 'info|' . (
                $hari === 1
                    ? 'Belum dimulai · Aktif 1 hari lagi'
                    : 'Belum dimulai · Aktif ' . $hari . ' hari lagi'
            );
        }

        // ⚠️ SUDAH KIRIM, BELUM DIPERIKSA (masih dalam waktu)
        if (
            $this->latestPengiriman &&
            !$this->latestPenerimaan &&
            $today->between($mulai, $selesai)
        ) {
            return 'warning|Pengiriman sedang diperiksa oleh ketua tim';
        }

        // ❌ TELAT (belum pernah kirim)
        if (!$this->latestPengiriman && $today->gt($selesai)) {
            return 'danger|Penugasan telah berakhir · Anda tidak mengirimkan penugasan';
        }

        return null; // aktif, tanpa tooltip
    }

}
