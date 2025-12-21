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
                    ->label(__('app.key'))
                    ->required(),
                TextInput::make('value')
                    ->label(__('app.value'))
                    ->required(),
                Select::make('type')
                    ->label(__('app.type'))
                    ->options(SettingType::class)
                    ->default(SettingType::Text->value)
                    ->required(),
            ]);
    }
}
