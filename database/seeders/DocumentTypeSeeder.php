<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypes = [
            [
                'key' => 'training_certificate',
                'name' => [
                    'en' => 'Training Certificate',
                    'ar' => 'شهادة تدريب',
                ],
            ],
            [
                'key' => 'accreditation_certificate',
                'name' => [
                    'en' => 'Accreditation Certificate',
                    'ar' => 'شهادة اعتماد',
                ],
            ],
            [
                'key' => 'experience_certificate',
                'name' => [
                    'en' => 'Experience Certificate',
                    'ar' => 'شهادة خبرة',
                ],
            ],
            [
                'key' => 'consultant_certificate',
                'name' => [
                    'en' => 'Consultant Certificate',
                    'ar' => 'شهادة استشاري',
                ],
            ],
            [
                'key' => 'accreditation_center',
                'name' => [
                    'en' => 'Accreditation Center Certificate',
                    'ar' => 'شهادة مركز اعتماد',
                ],
            ],
            [
                'key' => 'participation_certificate',
                'name' => [
                    'en' => 'Participation Certificate',
                    'ar' => 'شهادة مشاركة',
                ],
            ],
            [
                'key' => 'attendance_certificate',
                'name' => [
                    'en' => 'Attendance Certificate',
                    'ar' => 'شهادة حضور',
                ],
            ],
            [
                'key' => 'completion_certificate',
                'name' => [
                    'en' => 'Completion Certificate',
                    'ar' => 'شهادة إتمام',
                ],
            ],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::firstOrCreate(
                ['key' => $documentType['key']],
                $documentType
            );
        }
    }
}
