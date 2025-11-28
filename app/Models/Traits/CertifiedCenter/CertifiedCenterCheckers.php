<?php

declare(strict_types=1);

namespace App\Models\Traits\CertifiedCenter;

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

    public function canPerformActions(): bool
    {
        return $this->is_active && $this->isAccreditationActive();
    }
}
