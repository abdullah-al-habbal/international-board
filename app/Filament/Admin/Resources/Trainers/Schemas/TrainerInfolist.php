<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TrainerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            ImageEntry::make('avatar')
                ->label(__('app.avatar'))
                ->circular()
                ->getStateUsing(fn ($record) => $record->avatar_url)
                ->defaultImageUrl(url('assets/website/images/avatar.png'))
                ->columnSpanFull(),

            TextEntry::make('name')
                ->label(__('app.name'))
                ->weight('bold')
                ->size('lg')
                ->columnSpan(2),

            TextEntry::make('accreditation_number')
                ->label(__('app.accreditation_number'))
                ->icon('heroicon-o-identification')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('email')
                ->label(__('app.email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('phone')
                ->label(__('app.phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('country.name')
                ->label(__('app.country'))
                ->icon('heroicon-o-globe-alt')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('center.name')
                ->label(__('app.center'))
                ->icon('heroicon-o-building-office-2')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('address')
                ->label(__('app.address'))
                ->icon('heroicon-o-map-pin')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('bio')
                ->label(__('app.biography'))
                ->markdown()
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('specializations_count')
                ->label(__('app.specializations_count'))
                ->getStateUsing(fn ($record) => $record->specializations()->count())
                ->badge()
                ->color('primary')
                ->icon('heroicon-o-academic-cap')
                ->columnSpan(1),

            TextEntry::make('specializations.name')
                ->label(__('app.specializations'))
                ->badge()
                ->separator(',')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('certifications_count')
                ->label(__('app.certifications_count'))
                ->numeric()
                ->default(0)
                ->icon('heroicon-o-document-text')
                ->columnSpan(1),

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
        ])->columns(3);
    }
}
