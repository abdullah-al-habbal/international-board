<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypesSeeder extends Seeder
{
    public function run(): void
    {
        $distinctTypes = Certification::query()
            ->select('document_type')
            ->distinct()
            ->pluck('document_type')
            ->filter()
            ->unique();

        foreach ($distinctTypes as $type) {
            $key = Str::snake(Str::lower(trim($type)));

            DocumentType::firstOrCreate(
                ['key' => $key],
                [
                    'name' => [
                        'en' => $type,
                        'ar' => null,
                    ],
                ]
            );
        }

        $enumTypes = [
            'training_of_trainers' => 'Training of Trainers (TOT)',
            'accreditation_center' => 'Accreditation Center',
            'experience_certificate' => 'Experience Certificate',
            'adviser_certificate' => 'Adviser Certificate',
            'consultant_training' => 'Consultant Training',
            'specialization_certificate' => 'Specialization Certificate',
            'icdl_certificate' => 'ICDL Certificate',
            'basic_certificate' => 'Basic Certificate',
            'certificate' => 'Certificate',
            'diploma' => 'Diploma',
            'license' => 'License',
            'accreditation' => 'Accreditation',
        ];

        foreach ($enumTypes as $key => $name) {
            DocumentType::firstOrCreate(
                ['key' => $key],
                [
                    'name' => [
                        'en' => $name,
                        'ar' => null,
                    ],
                ]
            );
        }
    }
}
