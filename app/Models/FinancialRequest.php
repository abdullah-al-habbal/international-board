<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\NotifiesAdminOnMutation;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table('financial_requests')]
#[Fillable([
    'requestable_type',
    'requestable_id',
    'agent_person_id',
    'total_payment',
    'amount_paid',
    'reason',
    'date',
])]
#[Appends([
    'remaining_amount',
])]
class FinancialRequest extends Model
{
    use HasFactory;
    use NotifiesAdminOnMutation;

    protected function casts(): array
    {
        return [
            'total_payment' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_payment - $this->amount_paid;
    }

    public function requestable(): MorphTo
    {
        return $this->morphTo();
    }

    public function agentPerson(): BelongsTo
    {
        return $this->belongsTo(AgentPerson::class, 'agent_person_id');
    }
}
