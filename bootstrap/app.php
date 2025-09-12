<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(require __DIR__ . '/../config/middleware.php')
    ->withExceptions(require __DIR__ . '/../config/exceptions.php')
    ->withCommands(require __DIR__ . '/../config/commands.php')
    ->withSchedule(require __DIR__ . '/../config/schedule.php')
    ->create();
