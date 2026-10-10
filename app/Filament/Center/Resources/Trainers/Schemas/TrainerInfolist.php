<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TrainerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)->schema([
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
                ->placeholder('—'),

            TextEntry::make('email')
                ->label(__('app.email'))
                ->icon('heroicon-o-envelope')
                ->copyable()
                ->placeholder('—'),

            TextEntry::make('phone')
                ->label(__('app.phone'))
                ->icon('heroicon-o-phone')
                ->copyable()
                ->placeholder('—'),

            TextEntry::make('country.name')
                ->label(__('app.country'))
                ->icon('heroicon-o-globe-alt')
                ->placeholder('—'),

            TextEntry::make('trainerRole.name')
                ->label(__('app.trainer_role'))
                ->icon('heroicon-o-identification')
                ->badge()
                ->color('info')
                ->placeholder('—'),

            TextEntry::make('specializations.name')
                ->label(__('app.specializations'))
                ->badge()
                ->separator(',')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('accreditation_period_start')
                ->label(__('app.accreditation_period_start'))
                ->dateTime(),

            TextEntry::make('accreditation_period_end')
                ->label(__('app.accreditation_period_end'))
                ->dateTime(),

            TextEntry::make('created_at')
                ->label(__('app.created_at'))
                ->dateTime()
                ->icon('heroicon-o-calendar'),

            TextEntry::make('updated_at')
                ->label(__('app.updated_at'))
                ->dateTime()
                ->since()
                ->icon('heroicon-o-clock'),
        ]);
    }
}
