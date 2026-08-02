<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertifiedCenterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('manager_name')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(__('app.notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('logo')
                    ->label(__('app.logo'))
                    ->getStateUsing(fn ($record) => $record->logo_url)
                    ->defaultImageUrl(url('assets/website/images/avatar.png')),
                TextEntry::make('accreditation_period_start')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('accreditation_period_end')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('accreditation_number')
                    ->placeholder('-'),
                IconEntry::make('show_in_public_website')
                    ->label(__('app.show_in_public_website'))
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
