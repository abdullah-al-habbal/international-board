<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Models\Traits\AccreditationRequest\AccreditationRequestRelations;
use App\Models\Traits\AccreditationRequest\AccreditationRequestScopes;
use App\Observers\AccreditationRequestObserver;
use App\Policies\AccreditationRequestPolicy;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(AccreditationRequestPolicy::class)]
#[ObservedBy([AccreditationRequestObserver::class])]
class AccreditationRequest extends Model
{
    use AccreditationRequestRelations, AccreditationRequestScopes;
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
}
