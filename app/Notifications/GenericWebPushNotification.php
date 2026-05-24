<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class GenericWebPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly string $url,
        private readonly string $tag = 'pindang-oi',
        private readonly ?string $databaseNotificationId = null,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $notificationId = $this->databaseNotificationId ?? $notification->id;

        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/images/logo/logo-icon.svg')
            ->badge('/images/logo/logo-icon.svg')
            ->tag($this->tag.'-'.$notificationId)
            ->data([
                'url' => $this->url,
                'notification_id' => $notificationId,
            ])
            ->options(['TTL' => 3600]);
    }
}
