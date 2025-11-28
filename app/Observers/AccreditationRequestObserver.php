<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\AccreditationStarted;
use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use App\Notifications\AccreditationStatusChanged;
use Illuminate\Support\Facades\Auth;

final class AccreditationRequestObserver
{
    private const WEB_GUARD = 'web';

    public function creating(AccreditationRequest $accreditationRequest): void
    {
        $this->assignCenterIdIfNeeded($accreditationRequest);
    }

    public function created(AccreditationRequest $accreditationRequest): void
    {
        $this->dispatchAccreditationStartedEvent($accreditationRequest);
    }

    public function updating(AccreditationRequest $accreditationRequest): void
    {
        $this->setReviewDetailsIfStatusChanged($accreditationRequest);
    }

    public function updated(AccreditationRequest $accreditationRequest): void
    {
        $this->notifyStatusChangeIfNeeded($accreditationRequest);
    }

    private function assignCenterIdIfNeeded(AccreditationRequest $accreditationRequest): void
    {
        if ($this->shouldAssignCenterId($accreditationRequest)) {
            $center = $this->getAuthenticatedCenter();
            if ($center !== null) {
                $accreditationRequest->certified_center_id = $center->id;
            }
        }
    }

    private function shouldAssignCenterId(AccreditationRequest $accreditationRequest): bool
    {
        return ! $accreditationRequest->certified_center_id && $this->isWebGuardAuthenticated();
    }

    private function isWebGuardAuthenticated(): bool
    {
        return auth()->guard(self::WEB_GUARD)->check();
    }

    private function getAuthenticatedCenter(): ?CertifiedCenter
    {
        $user = auth()->guard(self::WEB_GUARD)->user();

        return $user instanceof CertifiedCenter ? $user : null;
    }

    private function dispatchAccreditationStartedEvent(AccreditationRequest $accreditationRequest): void
    {
        AccreditationStarted::dispatch($accreditationRequest);
    }

    private function setReviewDetailsIfStatusChanged(AccreditationRequest $accreditationRequest): void
    {
        if ($accreditationRequest->isDirty('status')) {
            $this->updateReviewDetails($accreditationRequest);
        }
    }

    private function updateReviewDetails(AccreditationRequest $accreditationRequest): void
    {
        $accreditationRequest->reviewed_at = now();
        $accreditationRequest->reviewed_by = Auth::id();
    }

    private function notifyStatusChangeIfNeeded(AccreditationRequest $accreditationRequest): void
    {
        if ($accreditationRequest->wasChanged('status')) {
            $this->sendStatusChangeNotification($accreditationRequest);
        }
    }

    private function sendStatusChangeNotification(AccreditationRequest $accreditationRequest): void
    {
        $accreditationRequest->certifiedCenter?->notify(
            new AccreditationStatusChanged($accreditationRequest)
        );
    }
}
