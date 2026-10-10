<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Certification;

use Illuminate\Foundation\Http\FormRequest;

final class CertificationSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accreditation_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}
