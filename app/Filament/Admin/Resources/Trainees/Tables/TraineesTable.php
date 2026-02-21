<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TraineesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->getStateUsing(fn($record) => $record->email ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn($record) => $record->phone ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('country.name')
                    ->label(__('Country'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->country->name ?? __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('nationality')
                    ->label(__('Nationality'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->nationality ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge()
                    ->getStateUsing(fn($record) => $record->gender ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('occupation')
                    ->label(__('Occupation'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->occupation ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('organization')
                    ->label(__('Organization'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->organization ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('date_of_birth')
                    ->label(__('Date of Birth'))
                    ->date()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->date_of_birth ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('certifications_count')
                    ->label(__('Certifications'))
                    ->counts('certifications')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('Country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options([
                        'male' => __('Male'),
                        'female' => __('Female'),
                    ])
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
