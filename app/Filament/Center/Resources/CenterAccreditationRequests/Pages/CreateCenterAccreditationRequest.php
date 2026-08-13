<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Filament\Center\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use App\Models\CertifiedCenter;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCenterAccreditationRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CenterAccreditationRequestResource::class;

    public function mount(): void
    {
        parent::mount();

        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if (! $center instanceof CertifiedCenter || ! $center->hasActiveAccreditationRequest()) {
            return;
        }

        $request = $center->activeAccreditationRequest();

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
        $data['certified_center_id'] = auth('certified_center')->id();
        $data['status'] = AccreditationStatus::Pending->value;

        unset($data['admin_notes'], $data['reviewed_by'], $data['reviewed_at']);

        return $data;
    }
}
