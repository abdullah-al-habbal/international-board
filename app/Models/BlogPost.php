<?php

// app/Models/BlogPost.php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['title', 'content', 'excerpt'])]
#[Fillable(['title', 'slug', 'excerpt', 'content', 'image', 'is_published', 'published_at'])]
class BlogPost extends Model
{
    use HasFactory, HasTranslations;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->attributes['image'] ?? null) {
            return Storage::url($this->attributes['image']);
        }

        return null;
    }
}
