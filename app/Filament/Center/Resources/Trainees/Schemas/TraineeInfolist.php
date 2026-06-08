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

            TextEntry::make('nationality')
                ->label(__('app.nationality'))
                ->icon('heroicon-o-flag')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('gender')
                ->label(__('app.gender'))
                ->badge()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('date_of_birth')
                ->label(__('app.date_of_birth'))
                ->date()
                ->icon('heroicon-o-calendar-days')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('occupation')
                ->label(__('app.occupation'))
                ->icon('heroicon-o-briefcase')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('organization')
                ->label(__('app.organization'))
                ->icon('heroicon-o-building-office')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('address')
                ->label(__('app.address'))
                ->icon('heroicon-o-map-pin')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('emergency_contact_name')
                ->label(__('app.emergency_contact_name'))
                ->icon('heroicon-o-user-group')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('emergency_contact_phone')
                ->label(__('app.emergency_contact_phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('medical_info')
                ->label(__('app.medical_info'))
                ->markdown()
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('notes')
                ->label(__('app.notes'))
                ->markdown()
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
        ])->columns(2);
    }
}
