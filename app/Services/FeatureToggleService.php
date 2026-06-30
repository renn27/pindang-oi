<?php

namespace App\Services;

use App\Models\Pegawai;

class FeatureToggleService
{
    /**
     * Konfigurasi aturan akses fitur (Feature Flag Registry).
     *
     * Format per fitur:
     * - 'enabled_for_all': jika true, dibuka untuk seluruh pegawai.
     * - 'allowed_nips': daftar NIP pegawai yang diperbolehkan mengakses (whitelist).
     * - 'allowed_roles': daftar role yang diperbolehkan mengakses (misal: 'SuperUser', 'Ketua Tim').
     */
    private static array $features = [
        'best_katim_leaderboard' => [
            'enabled_for_all' => false,
            'allowed_nips'    => [
                '340017814', // Pak Sukendro
            ],
            'allowed_roles'   => [],
        ],

        // 📝 CONTOH PENAMBAHAN FITUR BARU DI MASA DEPAN:
        // 'fitur_evaluasi_kedua' => [
        //     'enabled_for_all' => false,
        //     'allowed_nips'    => ['340017814', '340014387'], // Whitelist beberapa pegawai
        //     'allowed_roles'   => ['SuperUser'],            // Whitelist role
        // ],
    ];

    /**
     * Memeriksa apakah fitur tertentu aktif untuk pengguna saat ini.
     *
     * @param string $feature Nama kode fitur
     * @param Pegawai|null $user Model pegawai yang sedang login
     * @return bool
     */
    public static function isEnabled(string $feature, ?Pegawai $user = null): bool
    {
        // 1. Jika fitur tidak terdaftar di registry, default: matikan (false)
        if (!array_key_exists($feature, self::$features)) {
            return false;
        }

        $config = self::$features[$feature];

        // 2. Jika fitur dibuka untuk umum (global bypass)
        if (!empty($config['enabled_for_all'])) {
            return true;
        }

        if (!$user) {
            return false;
        }

        // 3. Cek Whitelist NIP Pegawai
        if (!empty($config['allowed_nips']) && in_array($user->nip_bps, $config['allowed_nips'])) {
            return true;
        }

        // 4. Cek Whitelist Role Pegawai
        if (!empty($config['allowed_roles'])) {
            foreach ($config['allowed_roles'] as $role) {
                // Cek role menggunakan method relasi bawaan model Pegawai
                if (method_exists($user, 'isActiveRole') && $user->isActiveRole($role)) {
                    return true;
                }
                if ($role === 'SuperUser' && method_exists($user, 'isSuperUser') && $user->isSuperUser()) {
                    return true;
                }
            }
        }

        return false;
    }
}
