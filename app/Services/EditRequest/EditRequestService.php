<?php

declare(strict_types=1);

namespace App\Services\EditRequest;

use App\Enums\EditRequestStatus;
use App\Repositories\EditRequest\EditRequestRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class EditRequestService
{
    public function __construct(
        private readonly EditRequestRepository $repository
    ) {}

    public function create(Model $editable, array $data): \App\Models\EditRequest
    {
        if ($this->hasPendingFor($editable)) {
            throw new \RuntimeException('An edit request is already pending for this entity.');
        }

        return $this->repository->create([
            'editable_id' => $editable->id,
            'editable_type' => $editable::class,
            'status' => EditRequestStatus::Pending->value,
            'data' => $data,
        ]);
    }

    public function approve(\App\Models\EditRequest $editRequest): bool
    {
        return $this->repository->updateStatus($editRequest, EditRequestStatus::Approved);
    }

    public function reject(\App\Models\EditRequest $editRequest, string $rejectionReason): bool
    {
        return $this->repository->updateStatus($editRequest, EditRequestStatus::Rejected, $rejectionReason);
    }

    public function hasPendingFor(Model $editable): bool
    {
        return $this->repository->findPendingFor($editable) !== null;
    }

    public function getPendingFor(Model $editable): ?\App\Models\EditRequest
    {
        return $this->repository->findPendingFor($editable);
    }
}
