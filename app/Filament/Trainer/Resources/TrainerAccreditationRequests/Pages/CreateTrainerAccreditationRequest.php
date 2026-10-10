<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\TrainerAccreditationRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use App\Models\Trainer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerAccreditationRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerAccreditationRequestResource::class;

    public function mount(): void
    {
        parent::mount();

        /** @var Trainer|null $trainer */
        $trainer = auth('trainer')->user();

        if (! $trainer instanceof Trainer || ! $trainer->hasActiveAccreditationRequest()) {
            return;
        }

        $request = $trainer->activeAccreditationRequest();

        if (in_array($request->status, [
            AccreditationStatus::Pending,
            AccreditationStatus::UnderReview,
        ], true)) {
            Notification::make()
                ->title(__('accreditation.messages.pending_title'))
                ->body(__('accreditation.messages.pending_body'))
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title(__('accreditation.messages.approved_title'))
                ->body(__('accreditation.messages.approved_body', [
                    'end_date' => $request->accreditation_end_date?->format('d/m/Y'),
                ]))
                ->warning()
                ->send();
        }

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['trainer_id'] = auth('trainer')->id();
        $data['status'] = AccreditationStatus::Pending->value;

        unset($data['admin_notes'], $data['reviewed_by'], $data['reviewed_at']);

        return $data;
    }
}
