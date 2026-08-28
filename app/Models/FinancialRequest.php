<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\NotifiesAdminOnMutation;
use App\Observers\FinancialRequestObserver;
use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table('financial_requests')]
#[ObservedBy([FinancialRequestObserver::class])]
#[Fillable([
    'requestable_type',
    'requestable_id',
    'agent_person_id',
    'currency_id',
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

    /**
     * Authoritative remaining balance.
     *
     * Returned as a fixed-scale decimal string, matching the `decimal:2` cast
     * on the columns it is derived from: `total_payment - amount_paid` on
     * floats would round (100.00 - 0.10 is not exactly 99.90 in binary), so the
     * subtraction runs in exact minor units. Filament's money formatter accepts
     * the numeric string unchanged.
     */
    public function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Money::subtract($this->total_payment, $this->amount_paid)
        );
    }

    /**
     * ISO code this record's amounts are denominated in.
     *
     * Reads the eager-loaded relation — never issues its own query — and falls
     * back to the configured code for rows predating the `currencies` table.
     */
    public function currencyCode(): string
    {
        return $this->currency?->code ?? Currency::fallbackCode();
    }

    public function requestable(): MorphTo
    {
        return $this->morphTo();
    }

    public function agentPerson(): BelongsTo
    {
        return $this->belongsTo(AgentPerson::class, 'agent_person_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    protected function casts(): array
    {
        return [
            'total_payment' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'date' => 'date',
        ];
    }
}
