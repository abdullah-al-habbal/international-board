<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAccreditationRequests\Tables;

use App\Enums\AccreditationStatus;
use App\Services\Accreditation\TrainerAccreditationApprovalService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TrainerAccreditationRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trainer.name')
                    ->label(__('app.trainer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (AccreditationStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->label(__('app.requested_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('accreditation_start_date')
                    ->label(__('app.start_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('accreditation_end_date')
                    ->label(__('app.end_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('app.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending || $record->status === AccreditationStatus::UnderReview)
                    ->form([
                        DateTimePicker::make('accreditation_end_date')
                            ->label(__('app.end_date'))
                            ->required()
                            ->after(fn () => now()),
                    ])
                    ->action(function ($record, array $data) {
                        app(TrainerAccreditationApprovalService::class)->approve(
                            $record,
                            $data['accreditation_end_date']
                        );
                        Notification::make()->success()->title(__('app.approved'))->send();
                    })
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->label(__('app.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending || $record->status === AccreditationStatus::UnderReview)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label(__('app.admin_notes'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(TrainerAccreditationApprovalService::class)->reject($record, $data['admin_notes']);
                        Notification::make()->danger()->title(__('app.rejected'))->send();
                    }),
                Action::make('under_review')
                    ->label(__('app.under_review'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== AccreditationStatus::UnderReview)
                    ->action(function ($record) {
                        $record->update([
                            'status' => AccreditationStatus::UnderReview->value,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()->info()->title(__('app.under_review'))->send();
                    }),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
