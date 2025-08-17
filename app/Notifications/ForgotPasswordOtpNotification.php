<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ForgotPasswordOtp;
use App\Models\User;

class ForgotPasswordOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ForgotPasswordOtp $otp;

    public function __construct(ForgotPasswordOtp $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail']; // Add 'sms' later if needed
    }

    public function toMail(User $notifiable): MailMessage
    {
        /** @var string */
        $appName = config('app.name') ?? 'Ilitio';

        return (new MailMessage())
            ->subject('Your Password Reset OTP')
            ->greeting('Hello!')
            ->line('Your One-Time Password (OTP) to reset your password is:')
            ->line("**{$this->otp->otp}**")
            ->line("This OTP will expire at **{$this->otp->expires_at->format('H:i A, d M Y')}**.")
            ->line('If you didn’t request this, please ignore this message.')
            ->salutation("Thanks,\n{$appName}");
    }
}
