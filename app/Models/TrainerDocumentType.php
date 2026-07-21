<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentTypeRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainerDocumentType extends Model
{
    use HasFactory;

    protected $table = 'trainer_document_types';

    protected $fillable = [
        'trainer_id',
        'key',
        'name',
        'status',
        'admin_notes',
        'reviewed_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'status' => DocumentTypeRequestStatus::class,
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'documentable');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }
}
