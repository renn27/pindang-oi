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
    }
}
