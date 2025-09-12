<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $memberships = [
            [
                'title' => [
                    'en' => 'Basic Membership',
                    'ar' => 'العضوية الأساسية'
                ],
                'description' => [
                    'en' => 'Basic membership with access to essential features.',
                    'ar' => 'عضوية أساسية مع الوصول للميزات الأساسية.'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => [
                    'en' => 'Premium Membership',
                    'ar' => 'العضوية المميزة'
                ],
                'description' => [
                    'en' => 'Premium membership with access to all features.',
                    'ar' => 'عضوية مميزة مع الوصول لجميع الميزات.'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => [
                    'en' => 'Corporate Membership',
                    'ar' => 'العضوية المؤسسية'
                ],
                'description' => [
                    'en' => 'Corporate membership for organizations.',
                    'ar' => 'عضوية مؤسسية للمنظمات.'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        $now = now();
        foreach ($memberships as &$membership) {
            $membership['created_at'] = $now;
            $membership['updated_at'] = $now;
        }

        Membership::insert($memberships);
    }
}
