<?php

declare(strict_types=1);

namespace App\Models\Traits\DocumentType;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait DocumentTypeRelations
{
    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }
}
