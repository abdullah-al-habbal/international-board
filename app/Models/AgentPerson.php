<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('agent_persons')]
#[Fillable([
    'name',
])]
class AgentPerson extends Model
{
    use HasFactory;

    public function financialRequests(): HasMany
    {
        return $this->hasMany(FinancialRequest::class);
    }

    public function centerFinancialRequests(): HasMany
    {
        return $this->hasMany(FinancialRequest::class)
            ->where('requestable_type', CertifiedCenter::class);
    }

    public function trainerFinancialRequests(): HasMany
    {
        return $this->hasMany(FinancialRequest::class)
            ->where('requestable_type', Trainer::class);
    }
}
