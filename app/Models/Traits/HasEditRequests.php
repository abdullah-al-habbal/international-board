<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\EditRequest;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasEditRequests
{
    public function editRequests(): MorphMany
    {
        return $this->morphMany(EditRequest::class, 'editable');
    }

    public function hasPendingEditRequest(): bool
    {
        return $this->editRequests()
            ->where('status', 'pending')
            ->exists();
    }

    public function getPendingEditRequest(): ?EditRequest
    {
        return $this->editRequests()
            ->where('status', 'pending')
            ->first();
    }
}
