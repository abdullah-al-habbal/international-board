<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use App\Models\Traits\AccreditationRequest\{AccreditationRequestRelations, AccreditationRequestScopes};
use App\Observers\AccreditationRequestObserver;
use App\Policies\AccreditationRequestPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\{UsePolicy, ObservedBy};

#[UsePolicy(AccreditationRequestPolicy::class)]
#[ObservedBy([AccreditationRequestObserver::class])]
class AccreditationRequest extends Model
{
    use HasFactory;
    use AccreditationRequestRelations, AccreditationRequestScopes;

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
