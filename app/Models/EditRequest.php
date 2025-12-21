<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EditRequestStatus;
use App\Policies\EditRequestPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UsePolicy(EditRequestPolicy::class)]
class EditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'editable_id',
        'editable_type',
        'status',
        'data',
        'rejection_reason',
        'approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EditRequestStatus::class,
            'data' => 'array',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function editable(): MorphTo
    {
        return $this->morphTo();
    }
}
