<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentTypeRequestStatus;
use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\TrainerDocumentTypeObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[ObservedBy([TrainerDocumentTypeObserver::class])]
#[Translatable(['name'])]
#[Table('trainer_document_types')]
#[Fillable([
    'trainer_id',
    'key',
    'name',
    'status',
    'admin_notes',
    'reviewed_by_admin_id',
])]
class TrainerDocumentType extends Model
{
    use HasFactory;
    use HasTranslations;
    use NotifiesAdminOnMutation;

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
