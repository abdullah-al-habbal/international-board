<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenters\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CertifiedCenterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('app.email'))
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label(__('app.password'))
                    ->password()
                    ->required(),
                Textarea::make('address')
                    ->label(__('app.address'))
                    ->columnSpanFull(),
                Select::make('country_id')
                    ->label(__('app.country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('phone')
                    ->label(__('app.phone'))
                    ->tel(),
                TextInput::make('manager_name')
                    ->label(__('app.manager_name')),
                DateTimePicker::make('accreditation_period_start')
                    ->label(__('app.accreditation_period_start')),
                DateTimePicker::make('accreditation_period_end')
                    ->label(__('app.accreditation_period_end')),
                TextInput::make('accreditation_number')
                    ->label(__('app.accreditation_number'))
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn($record) => $record !== null),
                Select::make('allowedDocumentTypes')
                    ->label(__('app.allowed_document_types'))
                    ->relationship('allowedDocumentTypes', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label(__('app.is_active'))
                    ->required(),
            ]);
    }
}
