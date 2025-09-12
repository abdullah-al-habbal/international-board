<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\StaticPage\StaticPageScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class StaticPage extends Model
{
    use HasFactory, HasTranslations, StaticPageScopes;

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
}
