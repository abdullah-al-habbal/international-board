<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\ArchitectureServiceProvider;
use App\Providers\EloquentServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CenterPanelProvider;
use App\Providers\MacroServiceProvider;
use App\Providers\ObserverServiceProvider;
use App\Providers\PerformanceServiceProvider;
use App\Providers\QueryServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\SecurityServiceProvider;
use App\Providers\ServiceRegistrationProvider;
use App\Providers\ValidationServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    AppServiceProvider::class,

    EloquentServiceProvider::class,
    QueryServiceProvider::class,
    SecurityServiceProvider::class,
    ValidationServiceProvider::class,
    ArchitectureServiceProvider::class,
    PerformanceServiceProvider::class,
    MacroServiceProvider::class,

    RepositoryServiceProvider::class,
    ServiceRegistrationProvider::class,

    ObserverServiceProvider::class,

    AdminPanelProvider::class,
    CenterPanelProvider::class,
    ViewServiceProvider::class,
];
