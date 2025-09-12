<?php

declare(strict_types=1);

namespace App\Models\Traits\CertifiedCenter;

use App\Models\AccreditationRequest;
use App\Models\Certification;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CertifiedCenterRelations
{
    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function accreditationRequests(): HasMany
    {
        return $this->hasMany(AccreditationRequest::class);
    }
}
