<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled file cleanup
Schedule::command('exec "find ' . base_path('storage/logs') . ' -type f -mtime +7 -delete"')->dailyAt('03:00');
Schedule::command('exec "find ' . base_path('storage') . ' -type f -name \"t.txt\" -delete"')->dailyAt('03:05');
