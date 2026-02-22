<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertifiedCentersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if (empty($record->email_verified_at)) {
                            return __('app.no_value');
                        }

                        return $record->email_verified_at;
                    }),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('manager_name')
                    ->searchable(),
                TextColumn::make('accreditation_period_start')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('accreditation_period_end')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('accreditation_number')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
