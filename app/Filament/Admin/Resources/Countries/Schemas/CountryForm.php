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
                    ->label(__('app.country_name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('code')
                    ->label(__('app.iso_code_3'))
                    ->required()
                    ->length(3)
                    ->unique(ignoreRecord: true)
                    ->placeholder('USA'),

                TextInput::make('code_2')
                    ->label(__('app.iso_code_2'))
                    ->required()
                    ->length(2)
                    ->unique(ignoreRecord: true)
                    ->placeholder('US'),

                TextInput::make('nationality')
                    ->label(__('app.nationality'))
                    ->maxLength(255)
                    ->placeholder(__('app.american')),

                Toggle::make('is_active')
                    ->label(__('app.active'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
