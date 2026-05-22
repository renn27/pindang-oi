<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Kegiatan::class => \App\Policies\KegiatanPolicy::class,
        \App\Models\SubKegiatan::class => \App\Policies\SubKegiatanPolicy::class,
        \App\Models\Penugasan::class => \App\Policies\PenugasanPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // WAJIB kalau pakai $policies
        $this->registerPolicies();

        // 🔥 Gate untuk kelola master data
        Gate::define('kelola-master-data', function ($user) {
            return in_array($user->active_role, ['Admin', 'Pimpinan']);
        });

        // 🔥 Gate untuk kelola pengumuman (Admin, Pimpinan, dan pegawai khusus Astri, A.Md.)
        Gate::define('kelola-pengumuman', function ($user) {
            // 1. Admin & Pimpinan selalu diizinkan
            if (in_array($user->active_role, ['Admin', 'Pimpinan'])) {
                return true;
            }
            // 2. Pengamanan kondisi extra untuk Astri, A.Md.
            return $user->nama_pegawai === 'Astri, A.Md.'
                && $user->nip === '198908012011012009'
                && $user->nip_bps === '340054675';
        });

        Gate::define('view-ckp', function ($user) {
            return !$user->isActiveRole('Admin');
        });

        // 🔥 Gate untuk kelola Kalender DL (Hanya Pimpinan)
        Gate::define('manage-kalender-dl', function ($user) {
            return $user->isActiveRole('Pimpinan');
        });
    }
}
