<?php

declare(strict_types=1);

use App\Jobs\ClearExpiredOtpsJob;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('telescope:prune')->daily();

Schedule::job(new ClearExpiredOtpsJob())->daily();
