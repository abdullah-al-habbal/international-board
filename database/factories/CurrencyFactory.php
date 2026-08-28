<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(Currency::class)]
class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        $code = strtoupper(fake()->unique()->bothify('??#'));

        return [
            // Translatable attributes are handed over as per-locale arrays.
            // Pre-encoding them to JSON would make HasTranslations store the
            // whole JSON document as the *current locale's* translation.
            'name' => ['en' => "Currency {$code}", 'ar' => "عملة {$code}"],
            'code' => $code,
            'symbol' => ['en' => "C{$code}", 'ar' => "ع{$code}"],
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (): array => [
            'code' => 'USD',
            'name' => ['en' => 'US Dollar', 'ar' => 'الدولار الأمريكي'],
            'symbol' => ['en' => '$', 'ar' => '$'],
            'is_default' => true,
        ]);
    }
}
