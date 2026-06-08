<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = array_map(fn (array $d) => [
            'key' => $d['key'],
            'name' => json_encode($d['name']),
        ], config('document_types'));

        DB::table('board_document_types')->insertOrIgnore($rows);
    }
}
