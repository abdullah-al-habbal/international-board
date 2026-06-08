<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Models\Country;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $centers = CertifiedCenter::all();
        $trainers = Trainer::all();
        $trainees = Trainee::all();
        $countries = Country::all();

        Certification::factory(5)
            ->recycle($users)
            ->recycle($centers)
            ->recycle($trainers)
            ->recycle($trainees)
            ->recycle($countries)
            ->create();
    }
}
