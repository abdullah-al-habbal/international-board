<?php

declare(strict_types=1);

namespace App\Models\Traits\Certification;

use App\Models\CertifiedCenter;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CertificationRelations
{
    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
