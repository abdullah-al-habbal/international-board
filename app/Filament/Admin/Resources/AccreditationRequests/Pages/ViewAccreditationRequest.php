<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AccreditationRequests\Pages;

use App\Enums\AccreditationStatus;
use App\Enums\CenterStatus;
use App\Filament\Admin\Resources\AccreditationRequests\AccreditationRequestResource;
use App\Models\AccreditationRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewAccreditationRequest extends ViewRecord
{
    protected static string $resource = AccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('approve')
                ->label(__('app.approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status !== AccreditationStatus::Approved)
                ->action(function (): void {
                    $this->approve($this->record);
                    $this->refreshFormData(['status', 'reviewed_by', 'reviewed_at']);
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


    private function approve(AccreditationRequest $request): void
    {
        $request->update([
            'status' => AccreditationStatus::Approved->value,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $center = $request->certifiedCenter;

        if ($center) {
            $center->update([
                'accreditation_period_start' => $request->requested_start_date,
                'accreditation_period_end' => $request->requested_end_date,
                'status' => CenterStatus::Active->value,
                'is_active' => true,
            ]);
        }
    }

    private function reject(AccreditationRequest $request): void
    {
        $request->update([
            'status' => AccreditationStatus::Rejected->value,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $center = $request->certifiedCenter;

        if (!$center) {
            return;
        }

        $hasOtherActive = $center->accreditationRequests()
            ->where('id', '!=', $request->id)
            ->where('status', AccreditationStatus::Approved)
            ->exists();

        if (!$hasOtherActive) {
            $center->update([
                'status' => CenterStatus::Suspended->value,
                'is_active' => false,
            ]);
        }
    }
}
