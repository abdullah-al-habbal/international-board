<?php

namespace App\Filament\Center\Resources\CenterTypeRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CenterTypeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('app.request_type'))
                    ->badge(),
                TextColumn::make('requested_name')
                    ->label(__('app.requested_name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge(),
                TextColumn::make('rejection_message')
                    ->label(__('app.rejection_message'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->rejection_message),
                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options(\App\Enums\CenterTypeRequestStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
