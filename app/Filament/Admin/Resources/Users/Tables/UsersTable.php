<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
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
                    ->copyable()
                    ->copyMessage(__('Email copied!')),

                TextColumn::make('type')
                    ->label(__('User Type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'client' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('email_verified_at')
                    ->label(__('Verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

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
                SelectFilter::make('type')
                    ->label(__('User Type'))
                    ->options([
                        'admin' => __('Admin'),
                        'client' => __('Client'),
                    ])
                    ->multiple(),

                TernaryFilter::make('email_verified_at')
                    ->label(__('Email Verification'))
                    ->placeholder(__('All Users'))
                    ->trueLabel(__('Verified Only'))
                    ->falseLabel(__('Unverified Only')),
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
