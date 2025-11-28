<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\DocumentType\DocumentTypeRelations;
use App\Models\Traits\DocumentType\DocumentTypeScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DocumentType extends Model
{
    use DocumentTypeRelations, DocumentTypeScopes;
    use HasFactory, HasTranslations;

    public $translatable = ['name'];

    protected $fillable = ['key', 'name'];
}
