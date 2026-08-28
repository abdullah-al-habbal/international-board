<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\CenterAccreditationRequestObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([CenterAccreditationRequestObserver::class])]
#[Fillable([
    'certified_center_id',
    'request_notes',
    'status',
    'admin_notes',
    'reviewed_by',
    'reviewed_at',
    'accreditation_start_date',
    'accreditation_end_date',
])]
class CenterAccreditationRequest extends Model
{
    use HasFactory;
    use NotifiesAdminOnMutation;

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'accreditation_start_date' => 'datetime',
            'accreditation_end_date' => 'datetime',
            'status' => AccreditationStatus::class,
        ];
    }

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Pending);
    }

    #[Scope]
    protected function underReview(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::UnderReview);
    }

    #[Scope]
    protected function approved(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Approved);
    }

    #[Scope]
    protected function rejected(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Rejected);
    }

    #[Scope]
    protected function forCenter(Builder $query, int $centerId): Builder
    {
        return $query->where('certified_center_id', $centerId);
    }
}
