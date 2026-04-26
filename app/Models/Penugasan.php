<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'butuh_translok',
        'status_translok',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        // 'butuh_dl' => 'boolean',
        // 'butuh_translok' => 'boolean',
    ];

    // RELATIONS
    public function anggota()
    { // pegawai diganti sebagai anggota supaya lebih jelas
        return $this->belongsTo(Pegawai::class, 'id_anggota', 'id_pegawai');
    }

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'id_sub_kegiatan', 'id_sub_kegiatan');
    }

    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan');
    }

    public function pengirimans()
    {
        return $this->hasMany(Pengiriman::class, 'id_penugasan', 'id_penugasan');
    }

    public function kalenderDLs()
    {
        return $this->hasMany(KalenderDL::class, 'id_penugasan');
    }

    public function ckpBulanan(): MorphMany
    {
        return $this->morphMany(CkpPegawai::class, 'ckpable');
    }

    public function ckp(): MorphOne
    {
        return $this->morphOne(CkpPegawai::class, 'ckpable');
    }

    /**
     * Hitung jumlah bulan pengiriman Diterima yang belum dijadikan CKP.
     * Digunakan untuk: policy setAsCKP, badge count, dan dropdown bulan CKP.
     */
    public function jumlahCkpBelumDibuat(): int
    {
        // Bulan-bulan yang sudah ada pengiriman Diterima
        $bulanDiterima = $this->pengirimans
            ->filter(fn($p) => $p->penerimaan && $p->penerimaan->status === 'Diterima')
            ->pluck('bulan_pengiriman')
            ->unique();

        // Bulan-bulan yang sudah punya CKP
        $bulanSudahCkp = $this->ckpBulanan->pluck('bulan_ckp')->unique();

        // Selisih = bulan yang diterima tapi belum CKP
        return $bulanDiterima->diff($bulanSudahCkp)->count();
    }

    /**
     * Ambil daftar bulan pengiriman Diterima yang belum dijadikan CKP.
     */
    public function bulanCkpBelumDibuat(): \Illuminate\Support\Collection
    {
        $bulanDiterima = $this->pengirimans
            ->filter(fn($p) => $p->penerimaan && $p->penerimaan->status === 'Diterima')
            ->pluck('bulan_pengiriman')
            ->unique();

        $bulanSudahCkp = $this->ckpBulanan->pluck('bulan_ckp')->unique();

        return $bulanDiterima->diff($bulanSudahCkp)->values();
    }
    // END RELATIONS

    protected static function booted()
    {
        static::created(function ($penugasan) {
            $pegawai = Pegawai::find($penugasan->id_anggota);

            if ($pegawai && $pegawai->active_role !== 'Anggota Tim' && $pegawai->id_pegawai !== auth()->user()?->id_pegawai) {
                $pegawai->update([
                    'active_role' => 'Anggota Tim'
                ]);
            }
        });

        // Tambahkan ini untuk mencegah terjadinya penghapusan data penugasan yang sudah masuk ke CKP Anggota Tim
        static::deleting(function ($penugasan) {
            if ($penugasan->ckp()->exists()) {
                throw new \RuntimeException('Penugasan tidak bisa dihapus karena sudah memiliki CKP.');
            }
        });
    }

    public function latestPengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'id_penugasan', 'id_penugasan')
            ->latestOfMany('created_at');
    }

    public function getBintangKirimArrayAttribute(): array
    {
        $filled = $this->latestPengiriman?->rating_kirim ?? 0;

        return array_map(
            fn($i) => $i <= $filled,
            range(1, 5)
        );
    }

    public function getBintangTerimaArrayAttribute(): array
    {
        $filled = $this->latestPenerimaan?->rating_terima ?? 0;

        return array_map(
            fn($i) => $i <= $filled,
            range(1, 5)
        );
    }

    public function latestPenerimaan()
    {
        return $this->hasOneThrough(
            Penerimaan::class,   // model akhir
            Pengiriman::class,   // model perantara
            'id_penugasan',      // FK di Pengiriman ke Penugasan
            'id_pengiriman',     // FK di Penerimaan ke Pengiriman
            'id_penugasan',      // local key di Penugasan
            'id_pengiriman'      // local key di Pengiriman
        )->latestOfMany('created_at');
    }

    public function isDinasLuar()
    {
        return $this->jenisKegiatan?->butuh_dl_atau_translok == 1;
    }

    public function sudahMasukKalenderDL()
    {
        return $this->kalenderDLs()->exists();
    }

    public function isStarted(): bool
    {
        return now()->gte($this->tanggal_mulai->copy()->startOfDay());
    }

    public function isEnded(): bool
    {
        return now()->gt($this->tanggal_selesai->copy()->endOfDay());
    }

    public function bolehTerimaPenugasan(): bool
    {
        $latestPengiriman = $this->latestPengiriman;

        // Tidak ada pengiriman → tidak bisa terima
        if (!$latestPengiriman) return false;

        // Sudah ada penerimaan untuk pengiriman terbaru → tidak bisa terima lagi
        if ($latestPengiriman->penerimaan) return false;

        // Sudah ada pelunasan diterima → tugas selesai
        $adaPelunasanDiterima = $this->pengirimans()
            ->where('tipe_pengiriman', 'Pelunasan')
            ->whereHas('penerimaan', fn($q) => $q->where('status', 'Diterima'))
            ->exists();

        if ($adaPelunasanDiterima) return false;

        return true;
    }

    public function tooltipPenerimaanPenugasan(): ?string
    {
        $today = Carbon::today();
        $mulai = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // ⏳ BELUM MULAI
        if (!$this->isStarted()) {
            $hari = now()->startOfDay()->diffInDays($mulai, false);

            return 'info|Penugasan Belum dimulai · Aktif ' . $hari . ' hari lagi';
        }

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // ❌ BELUM ADA PENGIRIMAN
        if (!$latestPengiriman) {
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

        // Sudah ada pelunasan diterima → tugas selesai
        $adaPelunasanDiterima = $this->pengirimans()
            ->where('tipe_pengiriman', 'Pelunasan')
            ->whereHas('penerimaan', fn($q) => $q->where('status', 'Diterima'))
            ->exists();

        if (!$adaPelunasanDiterima) return 'info|Tunggu  anggota tim mengirimkan Pelunasannya';

        // // ⚠️ DEADLINE LEWAT, SUDAH ADA PENGIRIMAN, BELUM DIPERIKSA
        // if  ($latestPengiriman && (! $latestPenerimaan ||
        //         $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)) {
        //     // Telat tapi masih diperiksa
        //     if ($today->gt($selesai)) {
        //         return 'danger|Anda sudah melewati batas waktu penerimaan penugasan';
        //     }
        //     // return 'danger|Pengiriman sudah masuk, namun tidak diperiksa oleh ketua tim';
        // }

        //  // ❌ PENUGASAN BERAKHIR & BELUM ADA PENGIRIMAN
        // if (! $latestPengiriman && $today->gt($selesai)) {
        //     return 'danger|Penugasan telah berakhir dan anggota tidak mengirimkannya';
        // }

        return null; // aktif, tanpa tooltip
    }

    public function bolehKirimPenugasan(): bool
    {
        // 1️⃣ BELUM DIMULAI → TUTUP BUTTON
        if (!$this->isStarted()) return false;

        // 2️⃣ Jenis DL tapi belum masuk kalender DL
        if ($this->isDinasLuar() && !$this->sudahMasukKalenderDL()) return false;

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // 2️⃣ Ada pengiriman terbaru tapi BELUM ada penerimaan untuk pengiriman tersebut
        if ($latestPengiriman && (!$latestPenerimaan || $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)) {
            return false; // sedang diperiksa
        }

        // 3️⃣ BARU: Jika sudah ada pelunasan yang diterima, blokir pengiriman
        $adaPelunasanDiterima = $this->pengirimans()
            ->where('tipe_pengiriman', 'Pelunasan')
            ->whereHas('penerimaan', fn($q) => $q->where('status', 'Diterima'))
            ->exists();

        if ($adaPelunasanDiterima) {
            return false;
        }

        // 4️⃣ Selain itu → boleh kirim
        return true;
    }

    public function tooltipPengirimanPenugasan(): ?string
    {
        $today = Carbon::today();
        $mulai = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // ⏳ BELUM MULAI
        if (!$this->isStarted()) {
            $hari = now()->startOfDay()->diffInDays($mulai, false);

            return 'info|Penugasan Belum dimulai · Aktif ' . $hari . ' hari lagi';
        }

        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        // ⚠️ Sedang diperiksa (kapan pun, termasuk lewat deadline)
        if (
            $latestPengiriman && (!$latestPenerimaan ||
                $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman)
        ) {
            // Telat tapi masih diperiksa
            if ($today->gt($selesai)) {
                return 'danger|Penerimaan sudah lewat batas waktu, tapi belum diterima ketua tim';
            }

            return 'warning|Pengiriman sedang diperiksa oleh ketua tim';
        }

        // 🟠 DL TAPI BELUM ACC PIMPINAN
        if ($this->isDinasLuar() && !$this->sudahMasukKalenderDL()) {
            return 'warning|Pengajuan DL masih menunggu persetujuan pimpinan';
        }

        // // ❌ BELUM KIRIM & SUDAH LEWAT DEADLINE
        // if (! $latestPengiriman && $today->gt($selesai)) {
        //     return 'danger|Belum ada pengiriman dan penugasan telah melewati batas waktu';
        // }

        // ✅ Sudah mulai, boleh kirim (telat / tidak → tanpa tooltip)
        return null;
    }

    public function statusPenugasan(): array
    {
        $today = Carbon::today();
        $deadline = Carbon::parse($this->tanggal_selesai)->startOfDay();
        $latestPengiriman = $this->latestPengiriman;
        $latestPenerimaan = $this->latestPenerimaan;

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ SUDAH KIRIM & SUDAH ADA PENERIMAAN
        |--------------------------------------------------------------------------
        */
        if ($latestPengiriman && $latestPenerimaan) {

            // Jika pengiriman terakhir LEBIH BARU dari penerimaan terakhir
            // Artinya: sudah kirim ulang, tapi belum ada respon
            if ($latestPengiriman->created_at->gt($latestPenerimaan->created_at)) {
                return [
                    'label' => 'Menunggu Penerimaan Lagi',
                    'class' => 'bg-yellow-100 text-yellow-700',
                ];
            }

            // Jika penerimaan terakhir adalah DITERIMA
            if ($latestPenerimaan->status === 'Diterima') {
                // CEK: apakah ini pelunasan atau cicilan?
                $tipePengiriman = $latestPengiriman->tipe_pengiriman;

                if ($tipePengiriman === 'Cicilan') {
                    return [
                        'label' => 'Diterima (Cicilan)',
                        'class' => 'bg-blue-100 text-blue-700',
                    ];
                }                

                return [
                    'label' => 'Diterima (Pelunasan)',
                    'class' => 'bg-green-200 text-green-800',
                ];
            }

            // Jika penerimaan terakhir adalah REVISI
            if ($latestPenerimaan->status === 'Revisi') {
                return [
                    'label' => 'Menunggu Pengiriman Ulang',
                    'class' => 'bg-orange-100 text-orange-700',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ DEADLINE LEWAT & TIDAK PERNAH KIRIM
        |--------------------------------------------------------------------------
        */
        // if ($today->gt($deadline) && ! $latestPengiriman) {
        //     return [
        //         'label' => 'Tidak Mengirimkan',
        //         'class' => 'bg-red-100 text-red-700',
        //     ];
        // }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ DEADLINE LEWAT, SUDAH KIRIM TAPI BELUM ADA PENERIMAAN
        |--------------------------------------------------------------------------
        */
        if ($today->gt($deadline) && $latestPengiriman && !$latestPenerimaan) {
            return [
                'label' => 'Belum Diterima Ketua Tim',
                'class' => 'bg-red-100 text-red-600',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ SUDAH KIRIM, MASIH DALAM DEADLINE, BELUM ADA PENERIMAAN
        |--------------------------------------------------------------------------
        */
        if ($latestPengiriman && !$latestPenerimaan) {
            return [
                'label' => 'Menunggu Penerimaan',
                'class' => 'bg-yellow-100 text-yellow-700',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ DEFAULT (BELUM DEADLINE & BELUM KIRIM)
        |--------------------------------------------------------------------------
        */
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
        if (
            $latestPengiriman && (
                !$latestPenerimaan ||
                $latestPenerimaan->id_pengiriman !== $latestPengiriman->id_pengiriman
            )
        ) {
            return [
                'label' => 'Menunggu Diperiksa',
                'class' => 'bg-yellow-100 text-yellow-600',
            ];
        }

        // 2️⃣ Penerimaan cocok dengan pengiriman terakhir
        if ($latestPenerimaan) {
            $label = $latestPenerimaan->status;
            $class = match ($latestPenerimaan->status) {
                'Diterima' => 'bg-green-100 text-green-700',
                'Revisi' => 'bg-red-100 text-red-500',
                default => 'bg-gray-100 text-gray-500',
            };

            // Diferensiasi Cicilan vs Pelunasan pada status Diterima
            if ($latestPenerimaan->status === 'Diterima' && $latestPengiriman) {
                if ($latestPengiriman->tipe_pengiriman === 'Cicilan') {
                    $label = 'Diterima (Cicilan)';
                    $class = 'bg-emerald-50 text-emerald-600';
                } else {
                    $label = 'Diterima (Pelunasan)';
                    $class = 'bg-green-100 text-green-700';
                }
            }

            return [
                'label' => $label,
                'class' => $class,
            ];
        }

        // 3️⃣ Belum ada apa-apa
        return [
            'label' => 'Belum Ada',
            'class' => 'bg-gray-100 text-gray-500',
        ];
    }
}
