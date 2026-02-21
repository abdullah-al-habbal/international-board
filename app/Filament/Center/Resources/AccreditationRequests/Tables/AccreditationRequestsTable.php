<?php

namespace App\Filament\Center\Resources\AccreditationRequests\Tables;

use App\Models\CertifiedCenter;
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
            ->modifyQueryUsing(function ($query) {
                if (auth()->guard('web')->check() && auth()->guard('web')->user() instanceof CertifiedCenter) {
                    $query->where('certified_center_id', auth()->guard('web')->id());
                }
            })
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
                TextColumn::make('reviewed_by')
                    ->numeric()
                    ->getStateUsing(fn($record) => $record->reviewed_by ?: __('app.no_value'))
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->getStateUsing(fn($record) => $record->reviewed_at ?: __('app.no_value'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
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
