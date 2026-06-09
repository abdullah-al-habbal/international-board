<?php

namespace App\Filament\Trainer\Resources\Certifications\Schemas;

use App\Models\Country;
use App\Models\Trainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([
            Hidden::make('creator_type')
                ->default(Trainer::class),
            Hidden::make('creator_id')
                ->default(fn () => Auth::guard('trainer')->id()),

            Select::make('trainee_id')
                ->label(__('app.trainee'))
                ->relationship('trainee', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label(__('app.name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label(__('app.email'))
                        ->email()
                        ->nullable()
                        ->unique('trainees', 'email'),
                    TextInput::make('phone')
                        ->label(__('app.phone'))
                        ->nullable(),
                    Select::make('country_id')
                        ->label(__('app.country'))
                        ->options(Country::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                    Textarea::make('notes')
                        ->label(__('app.notes'))
                        ->nullable(),
                ])
                ->required(),

            Select::make('country_id')
                ->label(__('app.country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('accredited_serial_number')
                ->label(__('app.accredited_serial_number'))
                ->required()
                ->maxLength(100),

            TextInput::make('document_code')
                ->label(__('app.document_code'))
                ->required()
                ->maxLength(50)
                ->default(fn () => Str::upper(Str::random(8))),

            TextInput::make('accreditation_number')
                ->label(__('app.accreditation_number'))
                ->nullable()
                ->maxLength(100),

            DatePicker::make('accreditation_date')
                ->label(__('app.accreditation_date'))
                ->required()
                ->default(now()),

            TextInput::make('paper_received')
                ->label(__('app.paper_received'))
                ->nullable()
                ->maxLength(10),

            Textarea::make('notes')
                ->label(__('app.notes'))
                ->nullable()
                ->columnSpanFull(),
        ]);
    }
}
