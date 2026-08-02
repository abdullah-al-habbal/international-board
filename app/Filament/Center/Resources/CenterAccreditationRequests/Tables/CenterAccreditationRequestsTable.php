<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CenterAccreditationRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('accreditation_start_date')
                    ->label(__('app.accreditation_start_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('accreditation_end_date')
                    ->label(__('app.accreditation_end_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label(__('app.reviewed_by'))
                    ->placeholder(__('app.no_value'))
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
            ])
            ->toolbarActions([
            ]);
    }
}
