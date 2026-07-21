<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $locale = app()->getLocale();

        $rows = array_map(fn (array $c) => [
            'name' => json_encode([$locale => $c['name']]),
            'code' => $c['code'],
            'code_2' => $c['code_2'],
            'is_active' => $c['is_active'],
        ], config('countries'));

        DB::table('countries')->insertOrIgnore($rows);
    }
}
