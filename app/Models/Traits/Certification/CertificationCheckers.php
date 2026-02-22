<?php

declare(strict_types=1);

namespace App\Models\Traits\Certification;

trait CertificationCheckers
{
    public function hasPaperReceived(): bool
    {
        if (is_string($this->paper_received)) {
            return in_array(strtoupper($this->paper_received), ['YES', 'YAS', '1', 'TRUE']);
        }

        return (bool) $this->paper_received;
    }

    public function isTrainingCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'training') ||
            str_contains($key, 'tot');
    }

    public function isAccreditationCenterCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'accreditation_center');
    }

    public function isExperienceCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'experience');
    }

    public function isConsultantCertificate(): bool
    {
        $key = strtolower($this->documentType?->key ?? '');

        return str_contains($key, 'adviser') ||
            str_contains($key, 'consultant');
    }

    public function hasValidData(): bool
    {
        return ! empty($this->trainee_id) &&
            ! empty($this->accredited_serial_number) &&
            ! empty($this->document_type_id) &&
            ! empty($this->accreditation_date);
    }

    public function isRecent(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(30));
    }

    public function isAccreditedInYear(int $year): bool
    {
        return $this->accreditation_date &&
            $this->accreditation_date->year === $year;
    }

    public function needsDataCleanup(): bool
    {
        return empty($this->document_type_id) ||
            empty($this->certified_center_id) ||
            $this->hasInconsistentNationality() ||
            $this->hasInconsistentPaperStatus();
    }

    private function hasInconsistentNationality(): bool
    {
        if (empty($this->nationality)) {
            return true;
        }

        $nationality = strtolower(trim($this->nationality));
        $standardNationalities = ['libyan', 'egyptian', 'syrian', 'yemeni', 'mauritanian'];

        return ! in_array($nationality, $standardNationalities) &&
            ! in_array($nationality, ['libya', 'egypt', 'syria', 'yemen', 'mauritania']);
    }

    private function hasInconsistentPaperStatus(): bool
    {
        if (empty($this->paper_received)) {
            return false;
        }

        $status = strtoupper(trim($this->paper_received));

        return ! in_array($status, ['YES', 'NO', 'TRUE', 'FALSE', '1', '0']);
    }
}
