<?php

declare(strict_types=1);

use App\Console\Commands\CheckAccreditationExpiry;
use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule): void {
    $schedule->command(CheckAccreditationExpiry::class)
        ->dailyAt('09:00')
        ->withoutOverlapping()
        ->onOneServer();
};
