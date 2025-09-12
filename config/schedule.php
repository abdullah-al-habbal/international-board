<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\CheckAccreditationExpiry;

return function (Schedule $schedule): void {
    $schedule->command(CheckAccreditationExpiry::class)
        ->dailyAt('09:00')
        ->withoutOverlapping()
        ->onOneServer();
};
