<?php

// app/Filament/Admin/Resources/Users/Tables/UsersTable.php
declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Enums\UserType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
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

                TextColumn::make('email')
                    ->label(__('app.email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->getStateUsing(fn ($record) => $record->email ?: __('app.no_value'))
                    ->copyMessage(__('app.email_copied')),

                TextColumn::make('type')
                    ->label(__('app.user_type'))
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof UserType ? $state->value : $state) {
                        'admin' => 'danger',
                        'client' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state instanceof UserType ? $state->label() : ($state ?: '—'))
                    ->sortable(),

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
                SelectFilter::make('type')
                    ->label(__('app.user_type'))
                    ->options([
                        'admin' => __('app.user_types.admin'),
                        'client' => __('app.user_types.client'),
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
