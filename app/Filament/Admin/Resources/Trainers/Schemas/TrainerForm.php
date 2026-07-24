<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Schemas;

use App\Models\Country;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('app.email'))
                ->email()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('phone')
                ->label(__('app.phone'))
                ->tel()
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('password')
                ->label(__('app.password'))
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->hint(fn (string $operation) => $operation === 'edit' ? __('app.password_keep_hint') : null)
                ->columnSpan(1),

            Select::make('country_id')
                ->label(__('app.country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->createOptionForm([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')
                        ->maxLength(3)
                        ->minLength(3)
                        ->alpha()
                        ->unique(Country::class, 'code')
                        ->helperText(__('app.iso_code_3_helper')),
                    TextInput::make('code_2')
                        ->maxLength(2)
                        ->minLength(2)
                        ->alpha()
                        ->unique(Country::class, 'code_2')
                        ->helperText(__('app.iso_code_2_helper')),
                ])
                ->columnSpan(1),

            Select::make('center_id')
                ->label(__('app.center'))
                ->relationship('center', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->columnSpan(1),

            FileUpload::make('avatar')
                ->label(__('app.avatar'))
                ->image()
                ->avatar()
                ->disk('public')
                ->directory('trainers/avatars')
                ->visibility('public')
                ->nullable()
                ->columnSpan(1),

            Textarea::make('address')
                ->label(__('app.address'))
                ->maxLength(65535)
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),

            Textarea::make('bio')
                ->label(__('app.biography'))
                ->maxLength(65535)
                ->rows(4)
                ->nullable()
                ->columnSpanFull(),

            Select::make('specializations')
                ->relationship('specializations', 'name')
                ->label(__('app.specializations'))
                ->multiple()
                ->searchable()
                ->preload()
                ->nullable()
                ->columnSpanFull(),

            DateTimePicker::make('accreditation_period_start')
                ->label(__('app.accreditation_period_start'))
                ->required()
                ->default(now())
                ->columnSpan(1),

            DateTimePicker::make('accreditation_period_end')
                ->label(__('app.accreditation_period_end'))
                ->required()
                ->after('accreditation_period_start')
                ->columnSpan(1),
        ])->columns(2);
    }
}
