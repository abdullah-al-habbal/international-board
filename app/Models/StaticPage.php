<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class StaticPage extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['title', 'content'];

    protected $fillable = [
        'slug',
        'title',
        'image',
        'content',
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
    protected function localizedSlug(Builder $query, string $slug): void
    {
        $query->where('slug->' . app()->getLocale(), $slug);
    }

    #[Scope]
    protected function bySlug(Builder $query, string $slug, ?string $locale = null): void
    {
        $locale ??= app()->getLocale();
        $query->where("slug->{$locale}", $slug);
    }

    #[Scope]
    protected function orderByTitle(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('title->' . app()->getLocale(), $direction);
    }
}
