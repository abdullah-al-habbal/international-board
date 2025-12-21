<?php

declare(strict_types=1);

namespace App\Repositories\CenterTypeRequest;

use App\Models\CenterTypeRequest;

final class CenterTypeRequestRepository
{
    public function __construct(private readonly CenterTypeRequest $model) {}

    public function all()
    {
        return $this->model->newQuery()->get();
    }

    public function find(int $id): ?CenterTypeRequest
    {
        return $this->model->find($id);
    }

    public function create(array $data): CenterTypeRequest
    {
        return $this->model->create($data);
    }

    public function update(CenterTypeRequest $request, array $data): bool
    {
        return $request->update($data);
    }

    public function delete(CenterTypeRequest $request): bool
    {
        return $request->delete();
    }

    public function findByCenter(int $centerId)
    {
        return $this->model->newQuery()
            ->where('certified_center_id', $centerId)
            ->get();
    }
}
