<?php

declare(strict_types=1);

namespace App\Models\Traits\DocumentType;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait DocumentTypeRelations
{
    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function centers(): BelongsToMany
    {
        return $this->belongsToMany(CertifiedCenter::class, 'certified_center_document_type');
    }
}
