<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CenterTypeRequest;
use App\Models\CertifiedCenter;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class CenterTypeRequestSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();
        $docTypes = DocumentType::all();
        CenterTypeRequest::factory(5)->recycle($centers)->recycle($docTypes)->create();
    }
}
