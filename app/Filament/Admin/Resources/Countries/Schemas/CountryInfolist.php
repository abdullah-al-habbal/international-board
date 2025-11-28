<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('Country Name')),

                TextEntry::make('code')
                    ->label(__('ISO Code (3 letters)'))
                    ->badge(),

                TextEntry::make('code_2')
                    ->label(__('ISO Code (2 letters)'))
                    ->badge(),

                TextEntry::make('nationality')
                    ->label(__('Nationality'))
                    ->placeholder('-'),

                IconEntry::make('is_active')
                    ->label(__('Status'))
                    ->boolean(),

                TextEntry::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
