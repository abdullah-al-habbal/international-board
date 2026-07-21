<?php

// app/Models/DocumentType.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

class DocumentType extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'board_document_types';

    public array $translatable = ['name'];

    protected $fillable = [
        'key',
        'name',
    ];

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'documentable');
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
        ];
    }
}
