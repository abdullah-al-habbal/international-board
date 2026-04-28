<?php
// app/Models/BlogPost.php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BlogPost extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'content', 'excerpt'];

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'is_published', 'published_at'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
