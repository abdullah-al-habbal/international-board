<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeFactory extends Factory
{
    protected $model = DocumentType::class;

    public function definition(): array
    {
        $key = $this->faker->unique()->slug(2);

        return [
            'key' => $key,
            'name' => [
                'en' => ucwords(str_replace('-', ' ', $key)),
                'ar' => 'نوع ' . $this->faker->word(),
            ],
        ];
    }
}
