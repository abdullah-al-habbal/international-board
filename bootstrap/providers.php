<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CenterPanelProvider;
use App\Providers\ObserverServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\ServiceRegistrationProvider;

return [
    AppServiceProvider::class,

    RepositoryServiceProvider::class,
    ServiceRegistrationProvider::class,

    ObserverServiceProvider::class,

    AdminPanelProvider::class,
    CenterPanelProvider::class,
];
