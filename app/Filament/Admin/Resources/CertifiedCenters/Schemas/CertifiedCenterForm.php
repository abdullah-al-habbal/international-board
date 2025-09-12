<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class Form
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('manager_name'),
                DateTimePicker::make('accreditation_period_start'),
                DateTimePicker::make('accreditation_period_end'),
                TextInput::make('accreditation_number'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
