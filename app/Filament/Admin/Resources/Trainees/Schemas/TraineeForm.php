<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TraineeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('phone')
                ->label(__('Phone'))
                ->tel()
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            Select::make('country_id')
                ->label(__('Country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->createOptionForm([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->required()->maxLength(10),
                    TextInput::make('code_2')->required()->maxLength(10),
                ])
                ->columnSpan(1),

            DatePicker::make('date_of_birth')
                ->label(__('Date of Birth'))
                ->nullable()
                ->columnSpan(1),

            TextInput::make('nationality')
                ->label(__('Nationality'))
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            Select::make('gender')
                ->label(__('Gender'))
                ->options([
                    'male' => __('Male'),
                    'female' => __('Female'),
                ])
                ->nullable()
                ->columnSpan(1),

            TextInput::make('occupation')
                ->label(__('Occupation'))
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('organization')
                ->label(__('Organization'))
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            Textarea::make('address')
                ->label(__('Address'))
                ->maxLength(65535)
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),

            TextInput::make('emergency_contact_name')
                ->label(__('Emergency Contact Name'))
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('emergency_contact_phone')
                ->label(__('Emergency Contact Phone'))
                ->tel()
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            Textarea::make('medical_info')
                ->label(__('Medical Information'))
                ->maxLength(65535)
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),

            Textarea::make('notes')
                ->label(__('Notes'))
                ->maxLength(65535)
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
        ])->columns(2);
    }
}
