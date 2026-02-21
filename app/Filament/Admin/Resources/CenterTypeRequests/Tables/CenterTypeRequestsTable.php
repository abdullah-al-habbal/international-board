<?php

namespace App\Filament\Admin\Resources\CenterTypeRequests\Tables;

use App\Enums\CenterTypeRequestStatus;
use App\Enums\CenterTypeRequestType;
use App\Services\CenterTypeRequest\CenterTypeRequestService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CenterTypeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('center.name')
                    ->label(__('app.certified_center'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('app.request_type'))
                    ->badge(),
                TextColumn::make('requested_name')
                    ->label(__('app.requested_name'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->requested_name ?: __('app.no_value')),
                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn(CenterTypeRequestStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options(CenterTypeRequestStatus::class),
                SelectFilter::make('type')
                    ->label(__('app.request_type'))
                    ->options(CenterTypeRequestType::class),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label(__('app.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === CenterTypeRequestStatus::Pending)
                    ->action(function ($record) {
                        app(CenterTypeRequestService::class)->approve($record);

                        Notification::make()
                            ->title(__('app.center_type_request_approved'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('app.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => $record->status === CenterTypeRequestStatus::Pending)
                    ->form([
                        Textarea::make('rejection_message')
                            ->label(__('app.rejection_message'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        app(CenterTypeRequestService::class)->reject($record, $data['rejection_message']);

                        Notification::make()
                            ->title(__('app.center_type_request_rejected'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
