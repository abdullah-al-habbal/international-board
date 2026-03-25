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
        // Unified empty-state formatter
        $empty = fn($state) => blank($state) ? __('app.no_value') : $state;

        return $table
            ->columns([
                TextColumn::make('name')
                    ->getStateUsing($empty)
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email address')
                    ->getStateUsing($empty)
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->getStateUsing($empty)
                    ->formatStateUsing($empty)
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('phone')
                    ->getStateUsing($empty)
                    ->searchable(),

                TextColumn::make('manager_name')
                    ->getStateUsing($empty)
                    ->searchable(),

                TextColumn::make('accreditation_period_start')
                    ->getStateUsing($empty)
                    ->formatStateUsing($empty)
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('accreditation_period_end')
                    ->getStateUsing($empty)
                    ->formatStateUsing($empty)
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('accreditation_number')
                    ->getStateUsing($empty)
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->getStateUsing($empty)
                    ->formatStateUsing($empty)
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->getStateUsing($empty)
                    ->formatStateUsing($empty)
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
