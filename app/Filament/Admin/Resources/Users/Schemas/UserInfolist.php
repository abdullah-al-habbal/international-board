<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('name')
                ->label(__('app.name'))
                ->weight('bold')
                ->size('xl')
                ->columnSpanFull()
                ->formatStateUsing(fn ($state, $record) => $state ?: ($record->name ?? '—')),

            TextEntry::make('email')
                ->label(__('app.email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->columnSpan(1)
                ->formatStateUsing(fn ($state) => $state ?: '—'),

            TextEntry::make('type')
                ->label(__('app.user_type'))
                ->badge()
                ->columnSpan(1)
                ->color(fn ($state): string => match ($state instanceof UserType ? $state->value : $state) {
                    'admin' => 'danger',
                    'client' => 'info',
                    default => 'gray',
                })
                ->formatStateUsing(fn ($state) => $state instanceof UserType ? ucfirst($state->value) : ($state ?: '—')),

            TextEntry::make('created_at')
                ->label(__('app.created_at'))
                ->dateTime()
                ->icon('heroicon-o-calendar')
                ->columnSpan(1),

            TextEntry::make('updated_at')
                ->label(__('app.updated_at'))
                ->dateTime()
                ->since()
                ->icon('heroicon-o-clock')
                ->columnSpan(1),

            TextEntry::make('id')
                ->label(__('app.id'))
                ->size('sm')
                ->columnSpan(1)
                ->formatStateUsing(fn ($state) => $state ?: '—'),
        ])->columns(2);
    }
}
