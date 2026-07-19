<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Membership\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MembershipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('title')
                ->label(__('app.title'))
                ->weight('bold')
                ->size('xl')
                ->columnSpanFull()
                ->formatStateUsing(fn ($record) => $record->getTranslation('title', app()->getLocale()) ?: '—'),

            TextEntry::make('slug')
                ->label(__('app.slug'))
                ->columnSpan(1)
                ->formatStateUsing(fn ($state) => $state ?: '—'),

            IconEntry::make('is_active')
                ->label(__('app.is_active'))
                ->boolean()
                ->columnSpan(1),

            TextEntry::make('description')
                ->label(__('app.description'))
                ->markdown()
                ->columnSpanFull()
                ->formatStateUsing(fn ($record) => $record->getTranslation('description', app()->getLocale()) ?: '—'),

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
                ->columnSpan(1),
        ])->columns(2);
    }
}
