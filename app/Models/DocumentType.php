<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\DocumentTypePolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['name'])]
#[Table('board_document_types')]
#[UsePolicy(DocumentTypePolicy::class)]
#[Fillable([
    'key',
    'name',
])]
class DocumentType extends Model
{
    use HasFactory;
    use HasTranslations;

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
