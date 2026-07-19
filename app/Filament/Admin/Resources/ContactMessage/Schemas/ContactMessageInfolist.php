<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ContactMessage\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('name')
                ->label(__('app.name'))
                ->weight('bold')
                ->size('xl')
                ->columnSpanFull(),

            TextEntry::make('email')
                ->label(__('app.email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->columnSpan(1),

            IconEntry::make('is_read')
                ->label(__('app.is_read'))
                ->boolean()
                ->columnSpan(1),

            TextEntry::make('message')
                ->label(__('app.message'))
                ->markdown()
                ->columnSpanFull(),

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
