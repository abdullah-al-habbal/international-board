<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Membership;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use App\Policies\CountryPolicy;
use App\Policies\DocumentTypePolicy;
use App\Policies\MembershipPolicy;
use App\Policies\TraineePolicy;
use App\Policies\TrainerPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Country::class => CountryPolicy::class,
        DocumentType::class => DocumentTypePolicy::class,
        Membership::class => MembershipPolicy::class,
        Trainee::class => TraineePolicy::class,
        Trainer::class => TrainerPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
