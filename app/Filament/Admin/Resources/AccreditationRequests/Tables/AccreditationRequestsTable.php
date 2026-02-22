<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AccreditationRequests\Tables;

use App\Enums\AccreditationStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccreditationRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certifiedCenter.name')
                    ->searchable(),
                TextColumn::make('requested_start_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('requested_end_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('reviewer.name')
                    ->label(__('app.reviewed_by'))
                    ->placeholder(__('app.no_value'))
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->placeholder(__('app.no_value'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('approve')
                    ->label(__('app.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => AccreditationStatus::Approved]))
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending || $record->status === AccreditationStatus::UnderReview),
                Action::make('reject')
                    ->label(__('app.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => AccreditationStatus::Rejected]))
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending || $record->status === AccreditationStatus::UnderReview),
                Action::make('under_review')
                    ->label(__('app.under_review'))
                    ->color('warning')
                    ->icon('heroicon-o-eye')
                    ->action(fn ($record) => $record->update(['status' => AccreditationStatus::UnderReview]))
                    ->visible(fn ($record) => $record->status === AccreditationStatus::Pending),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
