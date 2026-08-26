<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AccreditationStatus;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use App\Models\Trainer;
use App\Models\TrainerAccreditationRequest;
use App\Notifications\AdminActionNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrainerAccreditationRequestObserver
{
    public function creating(TrainerAccreditationRequest $request): void
    {
        $this->assertNoDuplicateActiveRequest($request);
        $this->assertNoTimeOverlap($request);
    }

    public function updating(TrainerAccreditationRequest $request): void
    {
        if ($request->isDirty('status')) {
            Log::channel('accreditation')->info('[Trainer] TrainerAccreditationRequest status transition', [
                'request_id' => $request->id,
                'trainer_id' => $request->trainer_id,
                'from' => $request->getOriginal('status') instanceof AccreditationStatus
                    ? $request->getOriginal('status')->value
                    : $request->getOriginal('status'),
                'to' => $request->status instanceof AccreditationStatus
                    ? $request->status->value
                    : $request->status,
            ]);
        }

        if ($request->isDirty(['accreditation_start_date', 'accreditation_end_date'])) {
            $this->assertNoTimeOverlap($request, excludeSelf: true);
        }
    }

    public function updated(TrainerAccreditationRequest $request): void
    {
        if (! $request->wasChanged('status')) {
            return;
        }

        $status = $request->status instanceof AccreditationStatus
            ? $request->status
            : AccreditationStatus::from($request->status);

        if ($status->isReviewed()) {
            $needsSave = false;
            if (empty($request->reviewed_by)) {
                $request->reviewed_by = Auth::id();
                $needsSave = true;
            }
            if (empty($request->reviewed_at)) {
                $request->reviewed_at = now();
                $needsSave = true;
            }
            if ($needsSave) {
                $request->saveQuietly();
            }
        }

        $trainer = $request->trainer;

        if (! $trainer) {
            Log::channel('accreditation')->warning('[Trainer] Request updated but trainer not found', [
                'request_id' => $request->id,
            ]);

            return;
        }

        match ($status) {
            AccreditationStatus::Approved => $this->handleApproved($request, $trainer),
            AccreditationStatus::Rejected => $this->handleRejected($request, $trainer),
            default => null,
        };

        if (Auth::guard('web')->check() && $status->isReviewed()) {
            $trainer->notify(new AdminActionNotification(
                $request,
                $status->value,
                'trainer',
                TrainerAccreditationRequestResource::class,
            ));
        }
    }

    private function assertNoDuplicateActiveRequest(TrainerAccreditationRequest $request): void
    {
        $hasActive = TrainerAccreditationRequest::query()
            ->where('trainer_id', $request->trainer_id)
            ->whereIn('status', [
                AccreditationStatus::Pending->value,
                AccreditationStatus::UnderReview->value,
            ])
            ->exists();

        $hasCurrentlyActiveApproved = TrainerAccreditationRequest::query()
            ->where('trainer_id', $request->trainer_id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('accreditation_start_date', '<=', today()->endOfDay())
            ->where('accreditation_end_date', '>=', today()->startOfDay())
            ->exists();

        if ($hasActive) {
            Log::channel('accreditation')->warning('[Trainer] Blocked duplicate pending request', [
                'trainer_id' => $request->trainer_id,
            ]);

            throw new \DomainException(__('accreditation.errors.pending_request_exists'));
        }

        if ($hasCurrentlyActiveApproved) {
            Log::channel('accreditation')->warning('[Trainer] Blocked duplicate active approved request', [
                'trainer_id' => $request->trainer_id,
            ]);

            throw new \DomainException(__('accreditation.errors.approved_request_exists'));
        }
    }

    private function assertNoTimeOverlap(TrainerAccreditationRequest $request, bool $excludeSelf = false): void
    {
        if (is_null($request->accreditation_start_date) || is_null($request->accreditation_end_date)) {
            return;
        }

        $query = TrainerAccreditationRequest::query()
            ->where('trainer_id', $request->trainer_id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('accreditation_start_date', '<=', $request->accreditation_end_date->copy()->endOfDay())
            ->where('accreditation_end_date', '>=', $request->accreditation_start_date->copy()->startOfDay());

        if ($excludeSelf) {
            $query->where('id', '!=', $request->id);
        }

        if ($query->exists()) {
            Log::channel('accreditation')->warning('[Trainer] Blocked overlapping accreditation period', [
                'trainer_id' => $request->trainer_id,
                'start' => $request->accreditation_start_date,
                'end' => $request->accreditation_end_date,
            ]);

            throw new \DomainException(__('accreditation.errors.time_overlap'));
        }
    }

    private function handleApproved(TrainerAccreditationRequest $request, Trainer $trainer): void
    {
        $trainer->accreditation_period_start = $request->accreditation_start_date;
        $trainer->accreditation_period_end = $request->accreditation_end_date;
        $trainer->saveQuietly();

        Log::channel('accreditation')->info('[Trainer] Accreditation approved', [
            'trainer_id' => $trainer->id,
            'period' => [
                'start' => $request->accreditation_start_date,
                'end' => $request->accreditation_end_date,
            ],
        ]);
    }

    private function handleRejected(TrainerAccreditationRequest $request, Trainer $trainer): void
    {
        $hasOtherActive = $trainer->accreditationRequests()
            ->where('id', '!=', $request->id)
            ->where('status', AccreditationStatus::Approved->value)
            ->where('accreditation_start_date', '<=', today()->endOfDay())
            ->where('accreditation_end_date', '>=', today()->startOfDay())
            ->exists();

        if (! $hasOtherActive) {
            $trainer->saveQuietly();

            Log::channel('accreditation')->info('[Trainer] Trainer deactivated after rejection', [
                'trainer_id' => $trainer->id,
            ]);
        }
    }
}
