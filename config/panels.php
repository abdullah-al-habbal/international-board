<?php

declare(strict_types=1);

return [
    'admin' => [
        'id' => 'admin',
        'path' => '/admin',
        'color' => 'amber',
        'label' => 'Admin Panel',
        'resources_path' => 'App\\Filament\\Admin\\Resources',
        'pages_path' => 'App\\Filament\\Admin\\Pages',
        'widgets_path' => 'App\\Filament\\Admin\\Widgets',
    ],
    'center' => [
        'id' => 'center',
        'path' => '/center',
        'color' => 'blue',
        'label' => 'Center Panel',
        'guard' => 'certified_center',
        'password_broker' => 'certified_centers',
        'resources_path' => 'App\\Filament\\Center\\Resources',
        'pages_path' => 'App\\Filament\\Center\\Pages',
        'widgets_path' => 'App\\Filament\\Center\\Widgets',
    ],
    'trainer' => [
        'id' => 'trainer',
        'path' => '/trainer',
        'color' => 'emerald',
        'label' => 'Trainer Panel',
        'guard' => 'trainer',
        'password_broker' => 'trainers',
        'resources_path' => 'App\\Filament\\Trainer\\Resources',
        'pages_path' => 'App\\Filament\\Trainer\\Pages',
        'widgets_path' => 'App\\Filament\\Trainer\\Widgets',
    ],
];
