<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
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
                    ->alpha()

                    ->unique(ignoreRecord: true)
                    ->placeholder('USA')
                    ->helperText(__('app.iso_code_3_helper')),

                TextInput::make('code_2')
                    ->label(__('app.iso_code_2'))
                    ->required()
                    ->length(2)
                    ->alpha()

                    ->unique(ignoreRecord: true)
                    ->placeholder('US')
                    ->helperText(__('app.iso_code_2_helper')),
            ]);
    }
}
