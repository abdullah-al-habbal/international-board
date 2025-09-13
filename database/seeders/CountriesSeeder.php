<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Libya', 'code' => 'LBY', 'code_2' => 'LY', 'nationality' => 'Libyan'],
            ['name' => 'Syria', 'code' => 'SYR', 'code_2' => 'SY', 'nationality' => 'Syrian'],
            ['name' => 'Egypt', 'code' => 'EGY', 'code_2' => 'EG', 'nationality' => 'Egyptian'],
            ['name' => 'Yemen', 'code' => 'YEM', 'code_2' => 'YE', 'nationality' => 'Yemeni'],
            ['name' => 'Mauritania', 'code' => 'MRT', 'code_2' => 'MR', 'nationality' => 'Mauritanian'],
            ['name' => 'Palestine', 'code' => 'PSE', 'code_2' => 'PS', 'nationality' => 'Palestinian'],
            ['name' => 'Oman', 'code' => 'OMN', 'code_2' => 'OM', 'nationality' => 'Omani'],
            ['name' => 'Tunisia', 'code' => 'TUN', 'code_2' => 'TN', 'nationality' => 'Tunisian'],
            ['name' => 'Jordan', 'code' => 'JOR', 'code_2' => 'JO', 'nationality' => 'Jordanian'],
            ['name' => 'Morocco', 'code' => 'MAR', 'code_2' => 'MA', 'nationality' => 'Moroccan'],
            ['name' => 'Netherlands', 'code' => 'NLD', 'code_2' => 'NL', 'nationality' => 'Dutch'], // Holland → Netherlands
            ['name' => 'India', 'code' => 'IND', 'code_2' => 'IN', 'nationality' => 'Indian'],
            ['name' => 'United Arab Emirates', 'code' => 'ARE', 'code_2' => 'AE', 'nationality' => 'Emirati'],
            ['name' => 'Lebanon', 'code' => 'LBN', 'code_2' => 'LB', 'nationality' => 'Lebanese'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
