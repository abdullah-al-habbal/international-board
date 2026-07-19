<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CenterAccreditationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'certified_center_id',
        'request_notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'accreditation_start_date',
        'accreditation_end_date',
    ];

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

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Pending);
    }

    public function scopeUnderReview(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::UnderReview);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Approved);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', AccreditationStatus::Rejected);
    }

    public function scopeForCenter(Builder $query, int $centerId): Builder
    {
        return $query->where('certified_center_id', $centerId);
    }
}
