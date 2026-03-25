<?php
// app/Models/CertifiedCenterDocumentType.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertifiedCenterDocumentType extends Model
{
    use HasFactory;

    protected $table = 'certified_center_document_types';

    protected $fillable = [
        'certified_center_id',
        'document_type_id',
    ];

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
