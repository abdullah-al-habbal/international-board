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
                    ->label(__('app.country_name')),

                TextEntry::make('code')
                    ->label(__('app.iso_code_3'))
                    ->badge(),

                TextEntry::make('code_2')
                    ->label(__('app.iso_code_2'))
                    ->badge(),

                IconEntry::make('is_active')
                    ->label(__('app.status'))
                    ->boolean(),

                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
