<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TrainersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label(__('app.avatar'))
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->avatar_url)
                    ->defaultImageUrl(url('assets/website/images/avatar.png'))
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('app.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('accreditation_number')
                    ->label(__('app.accreditation_number'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-identification')
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('email')
                    ->label(__('app.email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('app.phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn ($record) => $record->phone ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('country.name')
                    ->label(__('app.country'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('app.no_value'))
                    ->toggleable(),

                TextColumn::make('center.name')
                    ->label(__('app.center'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('app.no_center'))
                    ->toggleable(),

                TextColumn::make('specializations_count')
                    ->label(__('app.specializations_count'))
                    ->counts('specializations')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('specializations.name')
                    ->label(__('app.specializations'))
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->placeholder(__('app.no_value'))
                    ->toggleable(),

                TextColumn::make('certifications_count')
                    ->label(__('app.certifications'))
                    ->counts('certifications')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('show_in_public_website')
                    ->label(__('app.show_in_public_website'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('app.country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
