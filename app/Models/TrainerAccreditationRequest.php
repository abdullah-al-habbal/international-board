<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Observers\TrainerAccreditationRequestObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TrainerAccreditationRequestObserver::class])]
#[Fillable([
    'trainer_id',
    'request_notes',
    'status',
    'admin_notes',
    'reviewed_by',
    'reviewed_at',
    'accreditation_start_date',
    'accreditation_end_date',
])]
class TrainerAccreditationRequest extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => AccreditationStatus::class,
            'reviewed_at' => 'datetime',
            'accreditation_start_date' => 'datetime',
            'accreditation_end_date' => 'datetime',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
