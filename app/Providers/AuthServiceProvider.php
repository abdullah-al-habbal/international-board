<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\TrainerRole;
use App\Models\User;
use App\Policies\CountryPolicy;
use App\Policies\DocumentTypePolicy;
use App\Policies\TraineePolicy;
use App\Policies\TrainerRolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Country::class => CountryPolicy::class,
        DocumentType::class => DocumentTypePolicy::class,
        Trainee::class => TraineePolicy::class,
        TrainerRole::class => TrainerRolePolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
