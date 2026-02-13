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
                ->label(__('Name'))
                ->weight('bold')
                ->size('lg')
                ->columnSpan(2),

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

            TextEntry::make('nationality')
                ->label(__('Nationality'))
                ->icon('heroicon-o-flag')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('gender')
                ->label(__('Gender'))
                ->badge()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('date_of_birth')
                ->label(__('Date of Birth'))
                ->date()
                ->icon('heroicon-o-calendar-days')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('occupation')
                ->label(__('Occupation'))
                ->icon('heroicon-o-briefcase')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('organization')
                ->label(__('Organization'))
                ->icon('heroicon-o-building-office')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('address')
                ->label(__('Address'))
                ->icon('heroicon-o-map-pin')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('emergency_contact_name')
                ->label(__('Emergency Contact'))
                ->icon('heroicon-o-user-group')
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('emergency_contact_phone')
                ->label(__('Emergency Phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder('—')
                ->columnSpan(1),

            TextEntry::make('medical_info')
                ->label(__('Medical Information'))
                ->markdown()
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('notes')
                ->label(__('Notes'))
                ->markdown()
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
        ])->columns(2);
    }
}
