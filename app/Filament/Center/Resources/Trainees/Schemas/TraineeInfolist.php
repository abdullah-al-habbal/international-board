<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TraineeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('name')
                ->label(__('app.name'))
                ->weight('bold')
                ->size('lg')
                ->columnSpan(2),

            TextEntry::make('email')
                ->label(__('app.email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->placeholder(__('app.not_set'))
                ->columnSpan(1),

            TextEntry::make('phone')
                ->label(__('app.phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder(__('app.not_set'))
                ->columnSpan(1),

            TextEntry::make('country.name')
                ->label(__('app.country'))
                ->icon('heroicon-o-globe-alt')
                ->placeholder(__('app.not_set'))
                ->columnSpan(1),

            TextEntry::make('gender')
                ->label(__('app.gender'))
                ->badge()
                ->placeholder(__('app.not_set'))
                ->columnSpan(1),

            TextEntry::make('date_of_birth')
                ->label(__('app.date_of_birth'))
                ->date()
                ->icon('heroicon-o-calendar-days')
                ->placeholder(__('app.not_set'))
                ->columnSpan(1),

            TextEntry::make('notes')
                ->label(__('app.notes'))
                ->markdown()
                ->placeholder(__('app.not_set'))
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
        ])->columns(2);
    }
}
