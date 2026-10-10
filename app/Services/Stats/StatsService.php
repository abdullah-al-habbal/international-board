<?php

declare(strict_types=1);

namespace App\Services\Stats;

use App\Models\Trainee;
use App\Services\AccreditationRequest\AccreditationRequestService;
use App\Services\Certification\CertificationService;
use App\Services\CertifiedCenter\CertifiedCenterService;
use App\Services\Trainer\TrainerService;
use App\Services\User\UserService;

final class StatsService
{
    public function __construct(
        private readonly CertifiedCenterService $centerService,
        private readonly AccreditationRequestService $requestService,
        private readonly CertificationService $certificationService,
        private readonly UserService $userService,
        private readonly TrainerService $trainerService,
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'centers' => $this->getCenterStats(),
            'requests' => $this->getRequestStats(),
            'certifications' => $this->getCertificationStats(),
            'users' => $this->getUserStats(),
            'trainers' => $this->getTrainerStats(),
            'trainees' => [
                'total' => Trainee::query()->count(),
            ],
        ];
    }

    public function getCenterStats(): array
    {
        return [
            'total' => $this->centerService->getTotalCount(),
            'expired' => $this->centerService->getExpiredAccreditationCount(),
        ];
    }

    public function getRequestStats(): array
    {
        return [
            'total' => $this->requestService->getTotalCount(),
            'pending' => $this->requestService->getPendingCount(),
            'approved' => $this->requestService->getApprovedCount(),
            'rejected' => $this->requestService->getRejectedCount(),
        ];
    }

    public function getCertificationStats(): array
    {
        return [
            'total' => $this->certificationService->getTotalCount(),
            'this_month' => $this->certificationService->getCountThisMonth(),
        ];
    }

    public function getUserStats(): array
    {
        return [
            'total' => $this->userService->getTotalCount(),
            'admins' => $this->userService->getAdminCount(),
        ];
    }

    public function getTrainerStats(): array
    {
        return [
            'total' => $this->trainerService->getTotalCount(),
        ];
    }
}
