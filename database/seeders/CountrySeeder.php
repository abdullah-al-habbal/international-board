<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Libya',
                'code' => 'LBY',
                'code_2' => 'LY',
                'nationality' => 'Libyan',
                'is_active' => true,
            ],
            [
                'name' => 'Syria',
                'code' => 'SYR',
                'code_2' => 'SY',
                'nationality' => 'Syrian',
                'is_active' => true,
            ],
            [
                'name' => 'Egypt',
                'code' => 'EGY',
                'code_2' => 'EG',
                'nationality' => 'Egyptian',
                'is_active' => true,
            ],
            [
                'name' => 'Yemen',
                'code' => 'YEM',
                'code_2' => 'YE',
                'nationality' => 'Yemeni',
                'is_active' => true,
            ],
            [
                'name' => 'Palestine',
                'code' => 'PSE',
                'code_2' => 'PS',
                'nationality' => 'Palestinian',
                'is_active' => true,
            ],
            [
                'name' => 'Mauritania',
                'code' => 'MRT',
                'code_2' => 'MR',
                'nationality' => 'Mauritanian',
                'is_active' => true,
            ],
            [
                'name' => 'Oman',
                'code' => 'OMN',
                'code_2' => 'OM',
                'nationality' => 'Omani',
                'is_active' => true,
            ],
            [
                'name' => 'Saudi Arabia',
                'code' => 'SAU',
                'code_2' => 'SA',
                'nationality' => 'Saudi',
                'is_active' => true,
            ],
            [
                'name' => 'Jordan',
                'code' => 'JOR',
                'code_2' => 'JO',
                'nationality' => 'Jordanian',
                'is_active' => true,
            ],
            [
                'name' => 'Lebanon',
                'code' => 'LBN',
                'code_2' => 'LB',
                'nationality' => 'Lebanese',
                'is_active' => true,
            ],
            [
                'name' => 'Iraq',
                'code' => 'IRQ',
                'code_2' => 'IQ',
                'nationality' => 'Iraqi',
                'is_active' => true,
            ],
            [
                'name' => 'Kuwait',
                'code' => 'KWT',
                'code_2' => 'KW',
                'nationality' => 'Kuwaiti',
                'is_active' => true,
            ],
            [
                'name' => 'UAE',
                'code' => 'ARE',
                'code_2' => 'AE',
                'nationality' => 'Emirati',
                'is_active' => true,
            ],
            [
                'name' => 'Qatar',
                'code' => 'QAT',
                'code_2' => 'QA',
                'nationality' => 'Qatari',
                'is_active' => true,
            ],
            [
                'name' => 'Bahrain',
                'code' => 'BHR',
                'code_2' => 'BH',
                'nationality' => 'Bahraini',
                'is_active' => true,
            ],
            [
                'name' => 'Morocco',
                'code' => 'MAR',
                'code_2' => 'MA',
                'nationality' => 'Moroccan',
                'is_active' => true,
            ],
            [
                'name' => 'Tunisia',
                'code' => 'TUN',
                'code_2' => 'TN',
                'nationality' => 'Tunisian',
                'is_active' => true,
            ],
            [
                'name' => 'Algeria',
                'code' => 'DZA',
                'code_2' => 'DZ',
                'nationality' => 'Algerian',
                'is_active' => true,
            ],
            [
                'name' => 'Sudan',
                'code' => 'SDN',
                'code_2' => 'SD',
                'nationality' => 'Sudanese',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $countryData) {
            Country::firstOrCreate(
                ['name' => $countryData['name']],
                $countryData
            );
        }
    }
}
