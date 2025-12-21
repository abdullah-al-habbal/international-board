<?php

declare(strict_types=1);

namespace App\Models\Traits\CertifiedCenter;

use App\Models\AccreditationRequest;
use App\Models\CenterTypeRequest;
use App\Models\Certification;
use App\Models\Country;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function allowedDocumentTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'certified_center_document_type');
    }

    public function centerTypeRequests(): HasMany
    {
        return $this->hasMany(CenterTypeRequest::class);
    }
}
