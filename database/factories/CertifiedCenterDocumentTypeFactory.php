<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertifiedCenterDocumentTypeFactory extends Factory
{
    public function definition(): array
    {
        $key = fake()->unique()->slug(2);

        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'key' => $key,
            'name' => ['en' => ucwords(str_replace('-', ' ', $key)), 'ar' => 'نوع ' . fake()->word()],
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'admin_notes' => null,
            'reviewed_by_admin_id' => null,
        ];
    }
}
