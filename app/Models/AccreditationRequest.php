<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Policies\AccreditationRequestPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AccreditationRequestPolicy::class)]
class AccreditationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'certified_center_id',
        'requested_start_date',
        'requested_end_date',
        'request_notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_start_date' => 'datetime',
            'requested_end_date' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', AccreditationStatus::Approved)
            ->where('requested_start_date', '<=', Carbon::now())
            ->where('requested_end_date', '>=', Carbon::now());
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
