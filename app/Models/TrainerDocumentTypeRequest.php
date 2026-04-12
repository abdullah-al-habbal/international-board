<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentTypeRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerDocumentTypeRequest extends Model
{
    use HasFactory;

    protected $table = 'trainer_document_type_requests';

    protected $fillable = [
        'trainer_id',
        'requested_document_types',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_document_types' => 'array',
            'status' => DocumentTypeRequestStatus::class,
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }
}
