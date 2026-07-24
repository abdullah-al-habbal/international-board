<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenters\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->hint(fn (string $operation) => $operation === 'edit' ? __('app.password_keep_hint') : null),
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
                FileUpload::make('logo')
                    ->label(__('app.logo'))
                    ->image()
                    ->disk('public')
                    ->directory('centers/logos')
                    ->visibility('public')
                    ->nullable()
                    ->columnSpanFull(),
                DateTimePicker::make('accreditation_period_start')
                    ->label(__('app.accreditation_period_start')),
                DateTimePicker::make('accreditation_period_end')
                    ->label(__('app.accreditation_period_end')),
                TextInput::make('accreditation_number')
                    ->label(__('app.accreditation_number'))
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
