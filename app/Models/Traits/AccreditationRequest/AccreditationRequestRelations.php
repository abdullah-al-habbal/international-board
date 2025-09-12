<?php

declare(strict_types=1);

namespace App\Models\Traits\AccreditationRequest;

use App\Models\CertifiedCenter;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait AccreditationRequestRelations
{
    public function certifiedCenter(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
