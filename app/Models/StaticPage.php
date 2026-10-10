<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['title', 'content'])]
#[Fillable([
    'slug',
    'title',
    'image',
    'content',
])]
class StaticPage extends Model
{
    use HasFactory, HasTranslations;

    #[Scope]
    protected function localizedSlug(Builder $query, string $slug): void
    {
        $query->where('slug', $slug);
    }

    #[Scope]
    protected function orderByTitle(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('title->'.app()->getLocale(), $direction);
    }
}
