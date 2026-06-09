<?php

namespace App\Filament\Admin\Resources\CenterTypeRequests\Pages;

use App\Enums\CenterTypeRequestStatus;
use App\Filament\Admin\Resources\CenterTypeRequests\CenterTypeRequestResource;
use App\Services\CenterTypeRequest\CenterTypeRequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCenterTypeRequest extends ViewRecord
{
    protected static string $resource = CenterTypeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(__('app.approve'))
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === CenterTypeRequestStatus::Pending)
                ->action(function () {
                    app(CenterTypeRequestService::class)->approve($this->record);

                    Notification::make()
                        ->title(__('app.center_type_request_approved'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Action::make('reject')
                ->label(__('app.reject'))
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => $this->record->status === CenterTypeRequestStatus::Pending)
                ->form([
                    Textarea::make('rejection_message')
                        ->label(__('app.rejection_message'))
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    app(CenterTypeRequestService::class)->reject($this->record, $data['rejection_message']);

                    Notification::make()
                        ->title(__('app.center_type_request_rejected'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
