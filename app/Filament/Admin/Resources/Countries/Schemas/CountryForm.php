<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Country Name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('code')
                    ->label(__('ISO Code (3 letters)'))
                    ->required()
                    ->length(3)
                    ->unique(ignoreRecord: true)
                    ->placeholder('USA'),

                TextInput::make('code_2')
                    ->label(__('ISO Code (2 letters)'))
                    ->required()
                    ->length(2)
                    ->unique(ignoreRecord: true)
                    ->placeholder('US'),

                TextInput::make('nationality')
                    ->label(__('Nationality'))
                    ->maxLength(255)
                    ->placeholder(__('American')),

                Toggle::make('is_active')
                    ->label(__('Active'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
