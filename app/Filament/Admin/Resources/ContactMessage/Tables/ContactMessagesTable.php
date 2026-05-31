<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ContactMessage\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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

                TextColumn::make('message')
                    ->label(__('app.message'))
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn (Model $record): string => $record->message ?? ''),

                IconColumn::make('is_read')
                    ->label(__('app.is_read'))
                    ->boolean(),

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
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('toggleRead')
                    ->label(fn (Model $record) => $record->is_read
                        ? __('app.mark_as_unread')
                        : __('app.mark_as_read'))
                    ->icon(fn (Model $record) => $record->is_read
                        ? 'heroicon-o-eye-slash'
                        : 'heroicon-o-eye')
                    ->color(fn (Model $record) => $record->is_read ? 'gray' : 'success')
                    ->action(function (Model $record): void {
                        $record->update(['is_read' => !$record->is_read]);
                    }),
            ])
            ->toolbarActions([])   // no bulk delete or create
            ->defaultSort('created_at', 'desc');
    }
}