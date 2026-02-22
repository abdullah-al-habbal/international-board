<?php

// filePath: app/Models/Traits/CertifiedCenter/CertifiedCenterRelations.php
declare(strict_types=1);

namespace App\Models\Traits\CertifiedCenter;

use App\Models\AccreditationRequest;
use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CertifiedCenterRelations
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function accreditationRequests(): HasMany
    {
        return $this->hasMany(AccreditationRequest::class);
    }
}
