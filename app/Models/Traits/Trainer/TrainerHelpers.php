<?php

declare(strict_types=1);

namespace App\Models\Traits\Trainer;

trait TrainerHelpers
{
    public function getFullName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function hasContactInfo(): bool
    {
        return !empty($this->email) || !empty($this->phone);
    }

    public function getContactInfo(): array
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public function getAddressString(): ?string
    {
        if (empty($this->address)) {
            return null;
        }

        $address = is_array($this->address) ? $this->address : json_decode($this->address, true);

        if (!is_array($address)) {
            return null;
        }

        return implode(', ', array_filter([
            $address['street'] ?? null,
            $address['city'] ?? null,
            $address['state'] ?? null,
            $address['country'] ?? null,
            $address['postal_code'] ?? null,
        ]));
    }

    public function getSpecializationsList(): array
    {
        if (empty($this->specializations)) {
            return [];
        }

        return is_array($this->specializations) ? $this->specializations : json_decode($this->specializations, true) ?? [];
    }

    public function hasSpecialization(string $specialization): bool
    {
        $specializations = $this->getSpecializationsList();
        return in_array($specialization, $specializations);
    }

    public function addSpecialization(string $specialization): void
    {
        $specializations = $this->getSpecializationsList();

        if (!in_array($specialization, $specializations)) {
            $specializations[] = $specialization;
            $this->update(['specializations' => $specializations]);
        }
    }

    public function removeSpecialization(string $specialization): void
    {
        $specializations = $this->getSpecializationsList();
        $specializations = array_filter($specializations, fn($spec) => $spec !== $specialization);
        $this->update(['specializations' => array_values($specializations)]);
    }

    public function getCertificationsCount(): int
    {
        return $this->certifications()->count();
    }

    public function getRecentCertifications(int $limit = 5)
    {
        return $this->certifications()
            ->orderBy('accreditation_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getCertificationsByYear(int $year)
    {
        return $this->certifications()
            ->whereYear('accreditation_date', $year)
            ->get();
    }

    public function getCertificationsStats(): array
    {
        $total = $this->getCertificationsCount();
        $thisYear = $this->certifications()
            ->whereYear('accreditation_date', now()->year)
            ->count();
        $lastMonth = $this->certifications()
            ->whereMonth('accreditation_date', now()->subMonth()->month)
            ->whereYear('accreditation_date', now()->subMonth()->year)
            ->count();

        return [
            'total' => $total,
            'this_year' => $thisYear,
            'last_month' => $lastMonth,
        ];
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
