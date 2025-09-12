<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainer;

trait TrainerCheckers
{
    public function hasValidEmail(): bool
    {
        return !empty($this->email) && filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    public function hasValidPhone(): bool
    {
        if (empty($this->phone)) {
            return false;
        }

        // Basic phone validation - can be enhanced based on requirements
        $phone = preg_replace('/[^0-9+]/', '', $this->phone);
        return strlen($phone) >= 7 && strlen($phone) <= 15;
    }

    public function hasCompleteProfile(): bool
    {
        return !empty($this->name) &&
            !empty($this->email) &&
            !empty($this->phone) &&
            !empty($this->country_id);
    }

    public function hasSpecializations(): bool
    {
        $specializations = $this->getSpecializationsList();
        return !empty($specializations);
    }

    public function hasAddress(): bool
    {
        return !empty($this->address);
    }

    public function hasBio(): bool
    {
        return !empty($this->bio);
    }

    public function hasAvatar(): bool
    {
        return !empty($this->avatar);
    }

    public function isRecentlyActive(): bool
    {
        if (!$this->updated_at) {
            return false;
        }

        return $this->updated_at->isAfter(now()->subDays(30));
    }

    public function hasRecentCertifications(): bool
    {
        return $this->certifications()
            ->where('accreditation_date', '>=', now()->subDays(30))
            ->exists();
    }

    public function hasCertificationsThisYear(): bool
    {
        return $this->certifications()
            ->whereYear('accreditation_date', now()->year)
            ->exists();
    }

    public function isHighVolumeTrainer(): bool
    {
        $thisYearCount = $this->certifications()
            ->whereYear('accreditation_date', now()->year)
            ->count();

        return $thisYearCount >= 10; // Define threshold as needed
    }

    public function needsProfileUpdate(): bool
    {
        return empty($this->bio) ||
            empty($this->avatar) ||
            empty($this->specializations) ||
            !$this->hasValidEmail() ||
            !$this->hasValidPhone();
    }

    public function canBeDeactivated(): bool
    {
        // Check if trainer has recent certifications
        $hasRecentCertifications = $this->certifications()
            ->where('accreditation_date', '>=', now()->subDays(90))
            ->exists();

        return !$hasRecentCertifications;
    }

    public function hasIncompleteData(): bool
    {
        return empty($this->name) ||
            empty($this->email) ||
            empty($this->phone) ||
            empty($this->country_id) ||
            empty($this->specializations);
    }
}
