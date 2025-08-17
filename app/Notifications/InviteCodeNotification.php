<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\InviteCode;

class InviteCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public InviteCode $inviteCode;

    public function __construct(InviteCode $inviteCode)
    {
        $this->inviteCode = $inviteCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Add 'sms' later if needed
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var string */
        $appName = config('app.name') ?? 'Ilitio';

        $link = 'www.google.com';

        return (new MailMessage())
            ->subject('Your Invite Code')
            ->greeting('Hello!')
            ->line('Your Invite Code is:')
            ->line("**{$this->inviteCode->code}**")
            ->line('Please do not share this with anyone else. This code is usable only once.')
            ->line('If you haven\'t already, you can download the app from the link below.')
            ->action('Download the app', $link)
            ->salutation("Thanks,\n{$appName}");
    }
}
