<?php

declare(strict_types=1);

namespace App\Models\Traits\Country;

trait CountryRules
{
    public static function storeRules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:countries,name'],
            'code'        => ['required', 'string', 'size:3', 'unique:countries,code'],
            'code_2'      => ['required', 'string', 'size:2', 'unique:countries,code_2'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:countries,name,' . $id],
            'code'        => ['required', 'string', 'size:3', 'unique:countries,code,' . $id],
            'code_2'      => ['required', 'string', 'size:2', 'unique:countries,code_2,' . $id],
            'nationality' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ];
    }
}
