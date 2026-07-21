<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Schemas;

use App\Models\Country;
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
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('app.email'))
                ->email()
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('phone')
                ->label(__('app.phone'))
                ->tel()
                ->maxLength(255)
                ->nullable()
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
                        ->required()
                        ->maxLength(3)
                        ->minLength(3)
                        ->alpha()
                        ->unique(Country::class, 'code')
                        ->helperText(__('app.iso_code_3_helper')),
                    TextInput::make('code_2')
                        ->required()
                        ->maxLength(2)
                        ->minLength(2)
                        ->alpha()
                        ->unique(Country::class, 'code_2')
                        ->helperText(__('app.iso_code_2_helper')),
                ])
                ->columnSpan(1),

            DatePicker::make('date_of_birth')
                ->label(__('app.date_of_birth'))
                ->nullable()
                ->columnSpan(1),

            Select::make('gender')
                ->label(__('app.gender'))
                ->options([
                    'male' => __('app.male'),
                    'female' => __('app.female'),
                ])
                ->nullable()
                ->columnSpan(1),

            Textarea::make('address')
                ->label(__('app.address'))
                ->maxLength(65535)
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),

            Textarea::make('notes')
                ->label(__('app.notes'))
                ->maxLength(65535)
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
        ])->columns(2);
    }
}
