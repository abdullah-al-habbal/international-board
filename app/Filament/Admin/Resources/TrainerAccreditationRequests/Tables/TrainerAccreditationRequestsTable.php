<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAccreditationRequests\Tables;

use App\Enums\AccreditationStatus;
use App\Services\Accreditation\TrainerAccreditationApprovalService;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextColumn::make('requested_start_date')
                    ->label(__('app.start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('requested_end_date')
                    ->label(__('app.end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('app.requested_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('app.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending || $record->status === AccreditationStatus::UnderReview)
                    ->action(function ($record) {
                        app(TrainerAccreditationApprovalService::class)->approve($record);
                        Notification::make()->success()->title(__('app.approved'))->send();
                    })
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->label(__('app.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending || $record->status === AccreditationStatus::UnderReview)
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_notes')
                            ->label(__('app.admin_notes'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(TrainerAccreditationApprovalService::class)->reject($record, $data['admin_notes']);
                        Notification::make()->danger()->title(__('app.rejected'))->send();
                    }),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
