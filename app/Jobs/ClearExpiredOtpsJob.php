<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ForgotPasswordOtp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class ClearExpiredOtpsJob implements ShouldQueue
{
    use Queueable;

    /** Create a new job instance. */
    public function __construct()
    {

    }

    /** Execute the job. */
    public function handle(): void
    {
        /** @var int */
        $count = ForgotPasswordOtp::where('expires_at', '<', now())->delete();

        Log::info("ClearExpiredOtpsJob removed {$count} expired OTPs.");
    }
}
