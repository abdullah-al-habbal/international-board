<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Trainer;

use Illuminate\Foundation\Http\FormRequest;

final class TrainerIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'specialization' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return $this->only(['search', 'country_id', 'specialization']);
    }
}
