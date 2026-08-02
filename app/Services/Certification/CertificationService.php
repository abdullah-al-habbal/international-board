<?php

declare(strict_types=1);

namespace App\Services\Certification;

use App\Models\Certification;
use App\Repositories\Certification\CertificationRepository;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class CertificationService
{
    public function __construct(private readonly CertificationRepository $repo) {}

    public function getVerificationQrSvg(Certification $certification): ?string
    {
        $accreditationNumber = $certification->accreditation_number;

        if (blank($accreditationNumber)) {
            return null;
        }

        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'scale' => 5,
            'quietzoneSize' => 2,
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
        ]);

        return (new QRCode($options))->render(
            route('web.certifications.show', $accreditationNumber)
        );
    }

    public function getByCode(string $code): ?Certification
    {
        return $this->repo->findByDocumentCode($code);
    }

    public function getByAccreditationNumber(string $accreditationNumber): ?Certification
    {
        return $this->repo->findByAccreditationNumber($accreditationNumber);
    }

    public function getLatest()
    {
        return $this->repo->latest();
    }

    public function getTotalCount(): int
    {
        return $this->repo->getTotalCount();
    }

    public function getCountThisMonth(): int
    {
        return $this->repo->getCountThisMonth();
    }

    public function getCountByDateRange(\DateTime $startDate, \DateTime $endDate): int
    {
        return $this->repo->getCountByDateRange($startDate, $endDate);
    }

    public function getMonthlyCounts(?int $year = null): array
    {
        return $this->repo->getMonthlyCounts($year);
    }

    public function getMonthlyCountsByCenter(int $centerId, ?int $year = null): array
    {
        return $this->repo->getMonthlyCountsByCenter($centerId, $year);
    }

    public function getTotalCountByCenter(int $centerId): int
    {
        return $this->repo->getTotalCountByCenter($centerId);
    }

    public function getCountThisMonthByCenter(int $centerId): int
    {
        return $this->repo->getCountThisMonthByCenter($centerId);
    }

    public function getStatistics(): array
    {
        return $this->repo->getStatistics();
    }
}
