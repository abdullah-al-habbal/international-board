<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertifiedCenterFinancialRequest extends Model
{
    use HasFactory;

    protected $table = 'certified_center_financial_requests';

    protected $fillable = [
        'certified_center_id',
        'agent_person_id',
        'total_payment',
        'amount_paid',
        'reason',
        'date',
    ];

    protected $appends = [
        'remaining_amount',
    ];

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

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function agentPerson(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenterPaymentAgentPerson::class, 'agent_person_id');
    }
}
