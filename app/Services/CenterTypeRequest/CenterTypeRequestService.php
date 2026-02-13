<?php

declare(strict_types=1);

namespace App\Services\CenterTypeRequest;

use App\Enums\CenterTypeRequestStatus;
use App\Enums\CenterTypeRequestType;
use App\Models\DocumentType;
use App\Models\CenterTypeRequest;
use App\Repositories\CenterTypeRequest\CenterTypeRequestRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CenterTypeRequestService
{
    public function __construct(
        private readonly CenterTypeRequestRepository $repository
    ) {
    }

    public function create(array $data): CenterTypeRequest
    {
        return $this->repository->create($data);
    }

    public function approve(CenterTypeRequest $request): bool
    {
        return DB::transaction(function () use ($request) {
            $request->update([
                'status' => CenterTypeRequestStatus::Approved->value,
            ]);

            if ($request->type === CenterTypeRequestType::DocumentType->value) {
                $documentType = DocumentType::create([
                    'key' => Str::slug($request->requested_name),
                    'name' => [
                        'en' => $request->requested_name,
                        'ar' => $request->requested_name,
                    ],
                ]);

                $request->center->allowedDocumentTypes()->attach($documentType->id);
            } elseif ($request->type === CenterTypeRequestType::Course->value && $request->document_type_id) {
                $request->center->allowedDocumentTypes()->syncWithoutDetaching([$request->document_type_id]);
            }

            return true;
        });
    }

    public function reject(CenterTypeRequest $request, string $rejectionMessage): bool
    {
        return $request->update([
            'status' => CenterTypeRequestStatus::Rejected->value,
            'rejection_message' => $rejectionMessage,
        ]);
    }
}
