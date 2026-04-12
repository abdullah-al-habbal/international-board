<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccreditationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerAccreditationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
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
            'requested_start_date' => 'date',
            'requested_end_date' => 'date',
            'status' => AccreditationStatus::class,
            'reviewed_at' => 'datetime',
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
