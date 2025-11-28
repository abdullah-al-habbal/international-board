<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TrainerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            ImageEntry::make('avatar')
                ->label(__('Avatar'))
                ->circular()
                ->defaultImageUrl(url('/images/default-avatar.png'))
                ->columnSpanFull(),

            TextEntry::make('name')
                ->label(__('Name'))
                ->weight('bold')
                ->size('lg')
                ->columnSpan(2),

            IconEntry::make('is_active')
                ->label(__('Status'))
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger')
                ->columnSpan(1),

            TextEntry::make('email')
                ->label(__('Email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('phone')
                ->label(__('Phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('country.name')
                ->label(__('Country'))
                ->icon('heroicon-o-globe-alt')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('address')
                ->label(__('Address'))
                ->icon('heroicon-o-map-pin')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('bio')
                ->label(__('Biography'))
                ->markdown()
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('specializations')
                ->label(__('Specializations'))
                ->badge()
                ->separator(',')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('certifications_count')
                ->label(__('Certifications Count'))
                ->numeric()
                ->default(0)
                ->icon('heroicon-o-document-text')
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
        ])->columns(3);
    }
}
