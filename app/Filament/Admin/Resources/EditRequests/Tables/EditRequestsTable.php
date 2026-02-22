<?php

namespace App\Filament\Admin\Resources\EditRequests\Tables;

use App\Enums\EditRequestStatus;
use App\Services\EditRequest\EditRequestService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EditRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('editable_type')
                    ->label(__('app.editable_type'))
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->editable_type ?: __('app.no_value')),
                TextColumn::make('editable_id')
                    ->label(__('app.editable_id'))
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->editable_id ?: __('app.no_value')),
                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (EditRequestStatus $state): string => $state->color()),
                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options(EditRequestStatus::class),
                SelectFilter::make('editable_type')
                    ->label(__('app.editable_type'))
                    ->options([
                        'App\Models\CertifiedCenter' => __('app.certified_center'),
                        'App\Models\Trainer' => __('app.trainer'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label(__('app.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(EditRequestService::class)->approve($record);

                        Notification::make()
                            ->title(__('app.edit_request_approved'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('app.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('app.rejection_reason'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        app(EditRequestService::class)->reject($record, $data['rejection_reason']);

                        Notification::make()
                            ->title(__('app.edit_request_rejected'))
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
