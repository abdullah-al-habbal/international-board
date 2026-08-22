<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TrainerRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerRoleFactory extends Factory
{
    protected $model = TrainerRole::class;

    /** @var list<array{en: string, ar: string}> */
    private const ROLES = [
        ['en' => 'Sales Manager', 'ar' => 'مدير مبيعات'],
        ['en' => 'Senior Trainer', 'ar' => 'مدرب أول'],
        ['en' => 'Training Coordinator', 'ar' => 'منسق تدريب'],
        ['en' => 'Regional Manager', 'ar' => 'مدير إقليمي'],
        ['en' => 'Training Consultant', 'ar' => 'استشاري تدريب'],
        ['en' => 'Lead Instructor', 'ar' => 'محاضر رئيسي'],
    ];

    /**
     * Cycles through the fixed reference dataset. `trainer_roles.name` carries no
     * uniqueness constraint (parity with `specializations`), so wrapping past the
     * end of the list is valid and keeps large counts from overflowing.
     */
    public function definition(): array
    {
        static $index = 0;

        $role = self::ROLES[$index % count(self::ROLES)];
        $index++;

        return [
            'name' => $role,
        ];
    }
}
