<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertifiedCenterPaymentAgentPerson extends Model
{
    use HasFactory;

    protected $table = 'certified_center_payment_agent_persons';

    protected $fillable = [
        'name',
        'certified_center_id',
    ];

    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function centerFinancialRequests(): HasMany
    {
        return $this->hasMany(CertifiedCenterFinancialRequest::class, 'agent_person_id');
    }

    public function trainerFinancialRequests(): HasMany
    {
        return $this->hasMany(TrainerFinancialRequest::class, 'agent_person_id');
    }
}
