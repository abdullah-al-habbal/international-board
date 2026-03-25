<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name', 'nationality'];

    protected $fillable = [
        'name',
        'code',
        'code_2',
        'nationality',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function inactive(Builder $query): void
    {
        $query->where('is_active', false);
    }

    #[Scope]
    protected function byCode(Builder $query, string $code): void
    {
        $query->where('code', strtoupper($code));
    }

    #[Scope]
    protected function byCode2(Builder $query, string $code2): void
    {
        $query->where('code_2', strtoupper($code2));
    }

    #[Scope]
    protected function byName(Builder $query, string $name): void
    {
        $query->where('name', 'like', '%' . $name . '%');
    }

    #[Scope]
    protected function orderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('name', $direction);
    }

    #[Scope]
    protected function orderByCode(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('code', $direction);
    }

    public static function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:countries,name'],
            'code' => ['required', 'string', 'size:3', 'unique:countries,code'],
            'code_2' => ['required', 'string', 'size:2', 'unique:countries,code_2'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:countries,name,' . $id],
            'code' => ['required', 'string', 'size:3', 'unique:countries,code,' . $id],
            'code_2' => ['required', 'string', 'size:2', 'unique:countries,code_2,' . $id],
            'nationality' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
