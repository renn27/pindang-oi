<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Observers\ProfilDataObserver;
use App\Models\Announcement;
use App\Observers\AnnouncementObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\Events\NotificationSent;

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

        Event::listen(NotificationSent::class, function (NotificationSent $event) {
            Log::info('Web push sent.', [
                'subscribable_type' => $event->subscription->subscribable_type,
                'subscribable_id' => $event->subscription->subscribable_id,
                'endpoint' => $event->subscription->endpoint,
                'success' => $event->report->isSuccess(),
                'reason' => $event->report->getReason(),
            ]);
        });

        Event::listen(NotificationFailed::class, function (NotificationFailed $event) {
            Log::warning('Web push failed.', [
                'subscribable_type' => $event->subscription->subscribable_type,
                'subscribable_id' => $event->subscription->subscribable_id,
                'endpoint' => $event->subscription->endpoint,
                'success' => $event->report->isSuccess(),
                'reason' => $event->report->getReason(),
                'response' => $event->report->getResponseContent(),
            ]);
        });
    }

}
