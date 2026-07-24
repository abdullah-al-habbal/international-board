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
    ];

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
