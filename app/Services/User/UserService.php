<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Repositories\User\UserRepository;

final class UserService
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function getTotalCount(): int
    {
        return $this->userRepository->getTotalCount();
    }

    public function getAdminCount(): int
    {
        return $this->userRepository->getAdminCount();
    }

    public function getCountByType(string $type): int
    {
        return $this->userRepository->getCountByType($type);
    }

    public function findById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function findByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }
}
