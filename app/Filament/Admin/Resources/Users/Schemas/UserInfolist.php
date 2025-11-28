<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('name')
                ->label(__('Name'))
                ->weight('bold')
                ->size('lg')
                ->columnSpan(2),

            TextEntry::make('email')
                ->label(__('Email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->columnSpan(1),

            TextEntry::make('type')
                ->label(__('User Type'))
                ->badge()
                ->columnSpan(1),

            IconEntry::make('email_verified_at')
                ->label(__('Email Verified'))
                ->boolean()
                ->columnSpan(1),

            TextEntry::make('created_at')
                ->label(__('Created At'))
                ->dateTime()
                ->icon('heroicon-o-calendar')
                ->columnSpan(1),

            TextEntry::make('updated_at')
                ->label(__('Updated At'))
                ->dateTime()
                ->since()
                ->icon('heroicon-o-clock')
                ->columnSpan(1),

            TextEntry::make('email_verified_at')
                ->label(__('Email Verified At'))
                ->dateTime()
                ->placeholder('—')
                ->icon('heroicon-o-check-circle')
                ->columnSpanFull(),
        ])->columns(2);
    }
}
