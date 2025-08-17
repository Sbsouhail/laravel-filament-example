<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @property object{subject: string, description: string, user: User} $supportTicket
 */
class SupportTicketCreated extends Notification
{
    use Queueable;

    /** @var object{subject: string, description: string, user: User} */
    public object $supportTicket;

    /**
     * Create a new notification instance.
     *
     * @param object{subject: string, description: string, user: User} $supportTicket
     */
    public function __construct(object $supportTicket)
    {
        $this->supportTicket = $supportTicket;
    }

    /** Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    /** Get the mail representation of the notification. */
    public function toMail(): MailMessage
    {
        $ticket = $this->supportTicket;
        $user = $ticket->user;

        return (new MailMessage())
            ->subject('New Support Ticket: ' . $ticket->subject)
            ->greeting('Hello Admin,')
            ->line('A new support ticket has been submitted.')
            ->line('Subject: ' . $ticket->subject)
            ->line('Description: ' . $ticket->description)
            ->line('Submitted by: ' . $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')')
            ->line('Please address this request as soon as possible.');
    }
}
