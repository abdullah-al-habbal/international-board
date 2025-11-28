<?php

declare(strict_types=1);

namespace App\Models\Traits\Certification;

use App\Enums\CertificateType;

trait CertificationHelpers
{
    public function getDocumentTypeName(): ?string
    {
        return $this->documentType?->name;
    }

    public function getCertificateTypeEnum(): ?CertificateType
    {
        if (empty($this->certificate_type)) {
            return null;
        }

        return CertificateType::tryFrom($this->certificate_type);
    }

    public function isComplete(): bool
    {
        return ! empty($this->trainee_id) &&
            ! empty($this->accredited_serial_number) &&
            ! empty($this->accreditation_date) &&
            ! empty($this->document_type_id);
    }

    public function hasValidDate(): bool
    {
        if (empty($this->accreditation_date)) {
            return false;
        }

        return $this->accreditation_date >= '1900-01-01' &&
            $this->accreditation_date <= now();
    }

    public function getDataQualityScore(): int
    {
        $score = 0;
        $maxScore = 10;

        // Essential fields (5 points)
        if (! empty($this->trainee_id)) {
            $score += 1;
        }
        if (! empty($this->accredited_serial_number)) {
            $score += 1;
        }
        if (! empty($this->document_type_id)) {
            $score += 1;
        }
        if (! empty($this->accreditation_date)) {
            $score += 1;
        }
        if ($this->hasValidDate()) {
            $score += 1;
        }

        // Relationship fields (3 points)
        if (! empty($this->country_id)) {
            $score += 1;
        }
        if (! empty($this->trainer_id)) {
            $score += 1;
        }
        if (! empty($this->certified_center_id)) {
            $score += 1;
        }

        // Additional fields (2 points)
        if (! empty($this->paper_received)) {
            $score += 1;
        }
        if (! empty($this->notes)) {
            $score += 1;
        }

        return (int) round(($score / $maxScore) * 100);
    }

    public function getDataQualityStatus(): string
    {
        $score = $this->getDataQualityScore();

        return match (true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 60 => 'fair',
            $score >= 40 => 'poor',
            default => 'critical'
        };
    }
}
