<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertifiedCentersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->placeholder(__('app.no_value'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email address')
                    ->placeholder(__('app.no_value'))
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->placeholder(__('app.no_value'))
                    ->sortable(),

                TextColumn::make('phone')
                    ->placeholder(__('app.no_value'))
                    ->searchable(),

                TextColumn::make('manager_name')
                    ->placeholder(__('app.no_value'))
                    ->searchable(),

                TextColumn::make('notes')
                    ->label(__('app.notes'))
                    ->limit(50)
                    ->placeholder(__('app.no_value'))
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('logo')
                    ->label(__('app.logo'))
                    ->getStateUsing(fn ($record) => $record->logo_url)
                    ->defaultImageUrl(url('assets/website/images/avatar.png')),

                TextColumn::make('accreditation_period_start')
                    ->dateTime()
                    ->placeholder(__('app.no_value'))
                    ->sortable(),

                TextColumn::make('accreditation_period_end')
                    ->dateTime()
                    ->placeholder(__('app.no_value'))
                    ->sortable(),

                TextColumn::make('accreditation_number')
                    ->placeholder(__('app.no_value'))
                    ->searchable(),

                IconColumn::make('show_in_public_website')
                    ->label(__('app.show_in_public_website'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

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
