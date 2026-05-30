<?php
// filePath: routes/console.php
declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('exec "find '.base_path('storage/logs').' -type f -mtime +7 -delete"')->dailyAt('03:00');
Schedule::command('exec "find '.base_path('storage').' -type f -name \"t.txt\" -delete"')->dailyAt('03:05');
