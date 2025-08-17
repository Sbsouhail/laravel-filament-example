<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ForgotPasswordOtpNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordOtp extends Model
{
    protected $table = 'forgot_password_otps';

    protected $fillable = [
        'user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    protected static function booted(): void
    {
        static::creating(function (self $otp) {
            if (empty($otp->otp)) {
                $otp->otp = self::generateOtp();
            }
        });
    }

    /** Generate a 6-digit numeric OTP */
    protected static function generateOtp(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public static function addAndSend(int $userId): self
    {
        $otp = self::create([
            'user_id' => $userId,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Delete all other OTPs for the user except the newly created one
        self::where('user_id', $userId)
            ->where('id', '!=', $otp->id)
            ->delete();

        Notification::send($otp->user, new ForgotPasswordOtpNotification($otp));

        return $otp;
    }
}
