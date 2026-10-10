<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AgentPerson;
use Illuminate\Database\Seeder;

class AgentPersonSeeder extends Seeder
{
    public function run(): void
    {
        AgentPerson::factory(5)->create();
    }
}
