<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Schemas;

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
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('phone')
                ->label(__('app.phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('country.name')
                ->label(__('app.country'))
                ->icon('heroicon-o-globe-alt')
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('nationality')
                ->label(__('app.nationality'))
                ->icon('heroicon-o-flag')
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('gender')
                ->label(__('app.gender'))
                ->badge()
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('date_of_birth')
                ->label(__('app.date_of_birth'))
                ->icon('heroicon-o-calendar-days')
                ->placeholder(__('app.no_value'))
                ->columnSpan(1)
                ->formatStateUsing(function ($state) {
                    if (blank($state)) {
                        return __('app.no_value');
                    }
                    return $state;
                }),

            TextEntry::make('occupation')
                ->label(__('app.occupation'))
                ->icon('heroicon-o-briefcase')
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('organization')
                ->label(__('app.organization'))
                ->icon('heroicon-o-building-office')
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('address')
                ->label(__('app.address'))
                ->icon('heroicon-o-map-pin')
                ->placeholder(__('app.no_value'))
                ->columnSpanFull(),

            TextEntry::make('emergency_contact_name')
                ->label(__('app.emergency_contact_name'))
                ->icon('heroicon-o-user-group')
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('emergency_contact_phone')
                ->label(__('app.emergency_contact_phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('medical_info')
                ->label(__('app.medical_info'))
                ->markdown()
                ->placeholder(__('app.no_value'))
                ->columnSpanFull(),

            TextEntry::make('notes')
                ->label(__('app.notes'))
                ->markdown()
                ->placeholder(__('app.no_value'))
                ->columnSpanFull(),

            TextEntry::make('certifications_count')
                ->label(__('app.certifications'))
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
