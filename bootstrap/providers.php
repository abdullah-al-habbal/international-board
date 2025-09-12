<?php

declare(strict_types=1);

use App\Providers\{
    AppServiceProvider,
    ObserverServiceProvider,
    RepositoryServiceProvider,
    ServiceRegistrationProvider,
};

use App\Providers\Filament\{
    AdminPanelProvider,
    CenterPanelProvider
};

return [
    AppServiceProvider::class,

    RepositoryServiceProvider::class,
    ServiceRegistrationProvider::class,

    ObserverServiceProvider::class,

    AdminPanelProvider::class,
    CenterPanelProvider::class,
];
