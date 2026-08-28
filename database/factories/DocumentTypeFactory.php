<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(DocumentType::class)]
class DocumentTypeFactory extends Factory
{
    public function definition(): array
    {
        $key = $this->faker->unique()->slug(2);

        return [
            'key' => $key,
            'name' => [
                'en' => ucwords(str_replace('-', ' ', $key)),
                'ar' => 'نوع '.$this->faker->word(),
            ],
        ];
    }
}
