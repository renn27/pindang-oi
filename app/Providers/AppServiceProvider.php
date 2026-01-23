<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Observers\ProfilDataObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Pegawai::observe(ProfilDataObserver::class);
        Carbon::setLocale('id');
    }

    protected $policies = [
        \App\Models\Kegiatan::class => \App\Policies\KegiatanPolicy::class,
        \App\Models\SubKegiatan::class => \App\Policies\SubKegiatanPolicy::class,
    ];
}
