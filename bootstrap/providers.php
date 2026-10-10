<?php

// filePath: bootstrap/providers.php
declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\ArchitectureServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EloquentServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CenterPanelProvider;
use App\Providers\Filament\TrainerPanelProvider;
use App\Providers\MacroServiceProvider;
use App\Providers\PerformanceServiceProvider;
use App\Providers\QueryServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\SecurityServiceProvider;
use App\Providers\ServiceRegistrationProvider;
use App\Providers\ValidationServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    EloquentServiceProvider::class,
    QueryServiceProvider::class,
    SecurityServiceProvider::class,
    ValidationServiceProvider::class,
    ArchitectureServiceProvider::class,
    PerformanceServiceProvider::class,
    MacroServiceProvider::class,
    RepositoryServiceProvider::class,
    ServiceRegistrationProvider::class,
    AdminPanelProvider::class,
    CenterPanelProvider::class,
    TrainerPanelProvider::class,
    ViewServiceProvider::class,
];
