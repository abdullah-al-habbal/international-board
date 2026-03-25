<?php
// app/Models/CenterDocumentTypeRequest.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CenterDocumentTypeRequest extends Model
{
    use HasFactory;

    protected $table = 'center_document_type_requests';

    protected $fillable = [
        'certified_center_id',
        'requested_document_types',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_document_types' => 'array',
            'status' => 'string',
        ];
    }

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }
}
