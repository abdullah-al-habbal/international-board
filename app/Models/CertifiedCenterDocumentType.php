<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertifiedCenterDocumentType extends Model
{
    use HasFactory;

    protected $table = 'certified_center_document_types';

    protected $fillable = [
        'certified_center_id',
        'document_type_id',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'document_type_id', 'document_type_id');
    }
}
