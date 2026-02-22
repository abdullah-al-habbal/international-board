<?php

// filePath: app/Models/Traits/CertifiedCenter/CertifiedCenterCheckers.php
declare(strict_types=1);

namespace App\Models\Traits\CertifiedCenter;

use App\Enums\AccreditationStatus;
use App\Enums\PanelId;
use Carbon\Carbon;
use Filament\Panel;

trait CertifiedCenterCheckers
{
    public function canAccessPanel(Panel $panel): bool
    {
        return PanelId::tryFrom($panel->getId()) === PanelId::Center && $this->is_active;
    }

    public function isAccreditationActive(): bool
    {
        if (! $this->accreditation_period_start || ! $this->accreditation_period_end) {
            return false;
        }

        return Carbon::now()->between(
            $this->accreditation_period_start,
            $this->accreditation_period_end
        );
    }

    public function hasApprovedAccreditationRequest(): bool
    {
        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->exists();
    }

    /**
     * Determines whether the center has a "live" accreditation request that
     * acts as an active subscription: an Approved request whose requested
     * date range covers today, or at minimum an Approved request that has
     * not yet expired.
     */
    public function hasActiveAccreditationRequest(): bool
    {
        $now = Carbon::now();

        return $this->accreditationRequests()
            ->where('status', AccreditationStatus::Approved)
            ->where('requested_start_date', '<=', $now)
            ->where('requested_end_date', '>=', $now)
            ->exists();
    }

    public function canPerformActions(): bool
    {
        return $this->is_active
            && $this->isAccreditationActive()
            && $this->hasApprovedAccreditationRequest();
    }

    public function accreditationBlockReason(): ?string
    {
        if (! $this->is_active) {
            return __('accreditation.blocked.center_inactive');
        }

        if (! $this->hasApprovedAccreditationRequest()) {
            return __('accreditation.blocked.no_approved_request');
        }

        if (! $this->isAccreditationActive()) {
            return __('accreditation.blocked.period_expired');
        }

        return null;
    }
}
