<?php

// filePath: app/Filament/Center/Resources/AccreditationRequests/Tables/AccreditationRequestsTable.php
declare(strict_types=1);

namespace App\Filament\Center\Resources\AccreditationRequests\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccreditationRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('requested_start_date')
                    ->label(__('app.requested_start_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('requested_end_date')
                    ->label(__('app.requested_end_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('reviewed_at')
                    ->label(__('app.reviewed_at'))
                    ->dateTime()
                    ->placeholder(__('app.no_value'))
                    ->sortable(),

                TextColumn::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->placeholder(__('app.no_value'))
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                // Edit and Delete intentionally omitted — centers cannot modify submitted requests.
            ])
            ->toolbarActions([
                // No bulk actions for centers.
            ]);
    }
}
