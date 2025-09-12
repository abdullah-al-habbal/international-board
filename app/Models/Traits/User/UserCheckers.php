<?php

declare(strict_types=1);

namespace App\Models\Traits\User;

use App\Enums\PanelId;
use App\Enums\UserType;
use Filament\Panel;

trait UserCheckers
{
    public function canAccessPanel(Panel $panel): bool
    {
        return PanelId::tryFrom($panel->getId()) === PanelId::Admin
            && $this->type === UserType::Admin;
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::Admin;
    }

    public function isClient(): bool
    {
        return $this->type === UserType::Client;
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function isUnverified(): bool
    {
        return $this->email_verified_at === null;
    }
}
