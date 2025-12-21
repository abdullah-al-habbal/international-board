<?php

declare(strict_types=1);

namespace App\Repositories\EditRequest;

use App\Enums\EditRequestStatus;
use App\Models\EditRequest;
use Illuminate\Database\Eloquent\Model;

final class EditRequestRepository
{
    public function __construct(private readonly EditRequest $model) {}

    public function findPendingFor(Model $editable): ?EditRequest
    {
        return $this->model->newQuery()
            ->where('editable_id', $editable->id)
            ->where('editable_type', $editable::class)
            ->where('status', EditRequestStatus::Pending->value)
            ->first();
    }

    public function create(array $data): EditRequest
    {
        return $this->model->create($data);
    }

    public function updateStatus(EditRequest $editRequest, EditRequestStatus $status, ?string $rejectionReason = null): bool
    {
        $updateData = [
            'status' => $status->value,
        ];

        if ($status === EditRequestStatus::Approved) {
            $updateData['approved_at'] = now();
        } elseif ($status === EditRequestStatus::Rejected) {
            $updateData['rejected_at'] = now();
            if ($rejectionReason) {
                $updateData['rejection_reason'] = $rejectionReason;
            }
        }

        return $editRequest->update($updateData);
    }
}
