<?php

declare(strict_types=1);

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\User;

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'certified_center' => [
            'driver' => 'session',
            'provider' => 'certified_centers',
        ],
        'trainer' => [
            'driver' => 'session',
            'provider' => 'trainers',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
        'certified_centers' => [
            'driver' => 'eloquent',
            'model' => CertifiedCenter::class,
        ],
        'trainers' => [
            'driver' => 'eloquent',
            'model' => Trainer::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'certified_centers' => [
            'provider' => 'certified_centers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'trainers' => [
            'provider' => 'trainers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
