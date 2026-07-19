<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'type' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::factory()
            ->count(4)
            ->client()
            ->create();
    }
}
