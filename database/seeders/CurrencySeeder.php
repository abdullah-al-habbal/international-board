<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $currencies = collect(config('currencies.data'))
            ->map(static fn (array $currency): array => [
                ...$currency,
                'name' => json_encode(
                    $currency['name'],
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
                ),
                'symbol' => json_encode(
                    $currency['symbol'],
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
                ),
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        DB::table('currencies')->upsert(
            $currencies,
            ['code'],
            [
                'name',
                'symbol',
                'is_default',
                'updated_at',
            ],
        );
    }
}
