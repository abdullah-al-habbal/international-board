<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository
{
    public function __construct(private readonly User $model) {}

    public function getTotalCount(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getAdminCount(): int
    {
        return $this->model->admin()->count();
    }

    public function getClientCount(): int
    {
        return $this->model->client()->count();
    }

    public function getCountByType(UserType|string $type): int
    {
        return $this->model->ofType($type)->count();
    }

    public function getVerifiedCount(): int
    {
        return $this->model->verified()->count();
    }

    public function getUnverifiedCount(): int
    {
        return $this->model->unverified()->count();
    }

    public function findById(int $id): ?User
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->byEmail($email)->first();
    }

    public function getAdminsCreatedThisMonth(): Collection
    {
        return $this->model
            ->admin()
            ->createdThisMonth()
            ->orderByCreated()
            ->get();
    }

    public function getVerifiedAdmins(): Collection
    {
        return $this->model
            ->admin()
            ->verified()
            ->orderByName()
            ->get();
    }

    public function getUsersByType(UserType $type, bool $verified = true): Collection
    {
        $query = $this->model->ofType($type);

        if ($verified) {
            $query->verified();
        }

        return $query->orderByName()->get();
    }

    public function getRecentUsers(int $limit = 10): Collection
    {
        return $this->model
            ->newQuery()
            ->orderByCreated()
            ->limit($limit)
            ->get();
    }

    public function getUsersCreatedThisYear(): Collection
    {
        return $this->model
            ->createdThisYear()
            ->orderByCreated()
            ->get();
    }
}
