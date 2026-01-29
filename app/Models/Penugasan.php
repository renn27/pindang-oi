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
        'butuh_dl',
        'status_dl',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // RELATIONS
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

    public function kalenderDLs()
    {
        return $this->hasMany(KalenderDL::class, 'id_penugasan');
    }
    // END RELATIONS

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

    public function sudahMasukKalenderDL()
    {
        return $this->kalenderDLs()->exists();
    }


    public function isWithinSchedule(): bool
    {
        $today   = Carbon::today();
        $mulai   = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        return $today->between($mulai, $selesai);
    }

    public function bolehTerimaPenugasan(): bool
    {
        // 1️⃣ Belum masuk waktu
        if (! $this->isWithinSchedule()) {
            return false;
        }

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // 2️⃣ Belum ada pengiriman sama sekali
        if (! $latestPengiriman) {
            return false;
        }

        // // 3️⃣ Sudah ada penerimaan revisi, tapi BELUM ada pengiriman baru
        // if (
        //     $latestPenerimaan &&
        //     $latestPenerimaan->status === 'Revisi' &&
        //     $latestPenerimaan->id_pengiriman === $latestPengiriman->id_pengiriman
        // ) {
        //     return false; // tunggu anggota kirim ulang
        // }

        // // 4️⃣ Pengiriman terbaru BELUM pernah diterima → boleh terima
        // if (
        //     ! $latestPenerimaan ||
        //     $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman
        // ) {
        //     return true;
        // }
        // Belum ada penerimaan untuk pengiriman TERBARU
        return
            ! $latestPenerimaan ||
            $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman;

        // return false;
    }

    public function tooltipPenerimaanPenugasan(): ?string
    {
        $today   = Carbon::today();
        $mulai   = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // ⏳ BELUM MULAI
        if ($today->lt($mulai)) {
            $hari = $today->diffInDays($mulai);

            return 'info|' . (
                $hari === 1
                    ? 'Penugasan belum dimulai · Aktif 1 hari lagi'
                    : 'Penugasan belum dimulai · Aktif ' . $hari . ' hari lagi'
            );
        }

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // ❌ BELUM ADA PENGIRIMAN
        if (! $latestPengiriman && $this->isWithinSchedule()) {
            return 'info|Belum ada pengiriman dari anggota tim';
        }

        // ⚠️ SUDAH REVISI, TAPI BELUM ADA PENGIRIMAN ULANG
        if (
            $latestPengiriman &&
            $latestPenerimaan &&
            $latestPenerimaan->status === 'Revisi' &&
            $latestPenerimaan->id_pengiriman === $latestPengiriman->id_pengiriman
        ) {
            return 'warning|Tunggu anggota tim mengirimkan perbaikan';
        }

        // ⚠️ DEADLINE LEWAT, SUDAH ADA PENGIRIMAN, BELUM DIPERIKSA
        if (
            $latestPengiriman &&
            $today->gt($selesai) &&
            ( ! $latestPenerimaan || $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)
        ) {
            return 'danger|Pengiriman sudah masuk, namun tidak diperiksa oleh ketua tim';
        }

         // ❌ PENUGASAN BERAKHIR & BELUM ADA PENGIRIMAN
        if (! $latestPengiriman && $today->gt($selesai)) {
            return 'danger|Penugasan telah berakhir dan anggota tidak mengirimkannya';
        }

        return null; // aktif, tanpa tooltip
    }

    public function bolehKirimPenugasan(): bool
    {
        // 1️⃣ Belum masuk waktu
        if (! $this->isWithinSchedule()) {
            return false;
        }

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // 2️⃣ Ada pengiriman terbaru tapi BELUM ada penerimaan utk pengiriman tsb
        if ($latestPengiriman && (!$latestPenerimaan || $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)) {
            return false; // sedang diperiksa
        }

        // 3️⃣ Selain itu → boleh kirim
        return true;
    }

    public function tooltipPengirimanPenugasan(): ?string
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

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // ⚠️ Sedang diperiksa
        if ($latestPengiriman && ( !$latestPenerimaan || $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)
            & $today->between($mulai, $selesai)) {
            return 'warning|Pengiriman sedang diperiksa oleh ketua tim';
        }

        if (
            $latestPengiriman &&
            $today->gt($selesai) &&
            ( ! $latestPenerimaan || $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)
        ) {
            return 'danger|Pengiriman sudah masuk, namun tidak diperiksa ketua tim';
        }

        // ❌ TELAT
        if (!$latestPengiriman && $today->gt($selesai)) {
            return 'danger|Penugasan telah berakhir dan anda tidak mengirimkannya';
        }

        return null; // aktif, tanpa tooltip
    }

    public function statusPenugasan(): array
    {
        $today            = Carbon::today();
        $deadline         = Carbon::parse($this->tanggal_selesai);
        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // 1️⃣ TUGAS SELESAI
        if (
            $latestPengiriman &&
            $latestPenerimaan &&
            $latestPenerimaan->status === 'Diterima'
        ) {
            return [
                'label' => 'Tugas Selesai',
                'class' => 'bg-green-200 text-green-800',
            ];
        }

        // 2️⃣ DEADLINE LEWAT & TIDAK ADA PENGIRIMAN
        if ($today->gt($deadline) && ! $latestPengiriman) {
            return [
                'label' => 'Tidak Mengirimkan',
                'class' => 'bg-red-100 text-red-700',
            ];
        }

        // 3️⃣ DEADLINE LEWAT, SUDAH KIRIM TAPI BELUM DITERIMA
        if (
            $today->gt($deadline) &&
            $latestPengiriman &&
            (
                ! $latestPenerimaan ||
                $latestPenerimaan->status !== 'Diterima'
            )
        ) {
            return [
                'label' => 'Belum Diterima Ketua Tim',
                'class' => 'bg-red-100 text-red-600',
            ];
        }

        // 4️⃣ SUDAH KIRIM, BELUM DITERIMA, MASIH DALAM WAKTU
        if (
            $latestPengiriman &&
            (
                ! $latestPenerimaan ||
                $latestPenerimaan->status !== 'Diterima'
            )
        ) {
            return [
                'label' => 'Menunggu Penerimaan',
                'class' => 'bg-yellow-100 text-yellow-700',
            ];
        }

        // 5️⃣ DEFAULT (belum deadline & belum kirim)
        return [
            'label' => 'Menunggu Pengiriman',
            'class' => 'bg-gray-100 text-gray-700',
        ];
    }


    public function statusPengiriman(): array
    {
        return [
            'label' => $this->status,
            'class' => match ($this->status) {
                'Sudah Dikirim' => 'bg-blue-100 text-blue-600',
                default => 'bg-gray-100 text-gray-500',
            },
        ];
    }

    public function statusPenerimaan(): array
    {
        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // 1️⃣ Ada pengiriman terbaru tapi belum ada penerimaan utk pengiriman tsb
        if ($latestPengiriman && (
            !$latestPenerimaan ||
            $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman
        )) {
            return [
                'label' => 'Menunggu Diperiksa',
                'class' => 'bg-yellow-100 text-yellow-600',
            ];
        }

        // 2️⃣ Penerimaan cocok dengan pengiriman terakhir
        if ($latestPenerimaan) {
            return [
                'label' => $latestPenerimaan->status,
                'class' => match ($latestPenerimaan->status) {
                    'Diterima' => 'bg-green-100 text-green-700',
                    'Revisi'   => 'bg-red-100 text-red-500',
                    default    => 'bg-gray-100 text-gray-500',
                },
            ];
        }

        // 3️⃣ Belum ada apa-apa
        return [
            'label' => 'Belum Ada',
            'class' => 'bg-gray-100 text-gray-500',
        ];
    }
}
