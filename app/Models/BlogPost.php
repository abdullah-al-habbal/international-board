<?php
// app/Models/BlogPost.php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    use HasTranslations, HasFactory;

    public $translatable = ['title', 'content', 'excerpt'];

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'is_published', 'published_at'];

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
