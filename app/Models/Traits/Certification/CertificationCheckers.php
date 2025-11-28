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
        $documentType = strtolower($this->document_type ?? '');

        return str_contains($documentType, 'training of trainers') ||
            str_contains($documentType, 'tot');
    }

    public function isAccreditationCenterCertificate(): bool
    {
        $documentType = strtolower($this->document_type ?? '');

        return str_contains($documentType, 'accreditation center');
    }

    public function isExperienceCertificate(): bool
    {
        $documentType = strtolower($this->document_type ?? '');

        return str_contains($documentType, 'experience');
    }

    public function isConsultantCertificate(): bool
    {
        $documentType = strtolower($this->document_type ?? '');

        return str_contains($documentType, 'adviser') ||
            str_contains($documentType, 'consultant');
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
        return empty($this->certificate_type) ||
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
