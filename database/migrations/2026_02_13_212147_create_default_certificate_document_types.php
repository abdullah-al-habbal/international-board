<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    public function up(): void
    {
        Log::info('Creating default certificate document types...');

        $documentTypes = [
            [
                'key' => 'certificate_basic',
                'name' => json_encode([
                    'en' => 'Basic Certificate',
                    'ar' => 'شهادة أساسية',
                ]),
            ],
            [
                'key' => 'certificate_advanced',
                'name' => json_encode([
                    'en' => 'Advanced Certificate',
                    'ar' => 'شهادة متقدمة',
                ]),
            ],
            [
                'key' => 'certificate_professional',
                'name' => json_encode([
                    'en' => 'Professional Certificate',
                    'ar' => 'شهادة مهنية',
                ]),
            ],
            [
                'key' => 'certificate_specialist',
                'name' => json_encode([
                    'en' => 'Specialist Certificate',
                    'ar' => 'شهادة متخصص',
                ]),
            ],
        ];

        foreach ($documentTypes as $type) {
            DB::table('document_types')->updateOrInsert(
                ['key' => $type['key']],
                [
                    'name' => $type['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Log::info('Default certificate document types created successfully');
    }

    public function down(): void
    {
        DB::table('document_types')
            ->whereIn('key', [
                'certificate_basic',
                'certificate_advanced',
                'certificate_professional',
                'certificate_specialist',
            ])
            ->delete();

        Log::info('Default certificate document types removed');
    }
};
