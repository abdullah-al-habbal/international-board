<?php
declare(strict_types=1);

namespace App\Http\Requests\Web\CertifiedCenter;

use Illuminate\Foundation\Http\FormRequest;

final class CenterIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'     => ['nullable', 'string', 'max:100'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'page'       => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return $this->only(['search', 'country_id']);
    }
}
