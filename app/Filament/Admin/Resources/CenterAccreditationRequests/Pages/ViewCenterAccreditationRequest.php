<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterAccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Filament\Admin\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use App\Models\CenterAccreditationRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewCenterAccreditationRequest extends ViewRecord
{
    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('approve')
                ->label(__('app.approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    DateTimePicker::make('accreditation_end_date')
                        ->label(__('app.accreditation_end_date'))
                        ->required()
                        ->after(fn () => now()),
                ])
                ->visible(fn() => $this->record->status !== AccreditationStatus::Approved)
                ->action(function (array $data): void {
                    $this->approve($this->record, $data);
                    $this->refreshFormData(['status', 'reviewed_by', 'reviewed_at', 'accreditation_start_date', 'accreditation_end_date']);
                }),

            Action::make('reject')
                ->label(__('app.reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status !== AccreditationStatus::Rejected)
                ->action(function (): void {
                    $this->reject($this->record);
                    $this->refreshFormData(['status', 'reviewed_by', 'reviewed_at']);
                }),

            Action::make('under_review')
                ->label(__('app.under_review'))
                ->icon('heroicon-o-eye')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status !== AccreditationStatus::UnderReview)
                ->action(function (): void {
                    $this->record->update([
                        'status' => AccreditationStatus::UnderReview->value,
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'reviewed_by', 'reviewed_at']);
                }),
        ];
    }


    private function approve(CenterAccreditationRequest $request, array $data): void
    {
        $request->update([
            'status' => AccreditationStatus::Approved->value,
            'accreditation_start_date' => now(),
            'accreditation_end_date' => $data['accreditation_end_date'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }

    private function reject(CenterAccreditationRequest $request): void
    {
        $request->update([
            'status' => AccreditationStatus::Rejected->value,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }
}
