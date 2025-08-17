<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Log;

class HandleNotificationDelivery
{
    /** Create the event listener. */
    public function __construct()
    {

    }

    /** Handle the event when a notification fails. */
    public function handleNotificationFailed(NotificationFailed $event): void
    {
        Log::error('Notification failed: ', [
            'event' => get_class($event),
            'notifiable' => $event->notifiable,
            'notification' => $event->notification,
        ]);
    }

    /** Handle the event when a notification is sent. */
    public function handleNotificationSent(NotificationSent $event): void
    {
        Log::info('Notification sent: ', [
            'event' => get_class($event),
            'notifiable' => $event->notifiable,
            'notification' => $event->notification,
        ]);
    }
}
