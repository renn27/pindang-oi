<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Observers\ProfilDataObserver;
use App\Models\Announcement;
use App\Observers\AnnouncementObserver;

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
        Announcement::observe(AnnouncementObserver::class);
        Carbon::setLocale('id');
    }

}
