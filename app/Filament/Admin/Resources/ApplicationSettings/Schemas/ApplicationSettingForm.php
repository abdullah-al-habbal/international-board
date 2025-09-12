<?php

namespace App\Filament\Admin\Resources\ApplicationSettings\Schemas;

use App\Enums\SettingType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApplicationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                TextInput::make('value')
                    ->required(),
                Select::make('type')
                    ->options(SettingType::class)
                    ->default('text')
                    ->required(),
            ]);
    }
}
