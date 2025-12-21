<?php

namespace App\Filament\Center\Resources\Certifications\Schemas;

use App\Enums\CertificateType;
use App\Models\{CertifiedCenter, Country, Trainer};
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.import_page.instructions.heading'))
                    ->description(__('app.certification_details_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('certified_center_id')
                                    ->label(__('app.certified_center'))
                                    ->relationship('certifiedCenter', 'name')
                                    ->default(fn () =>
                                        Auth::guard('web')->check() && Auth::guard('web')->user() instanceof CertifiedCenter
                                            ? Auth::guard('web')->id()
                                            : null
                                    )
                                    ->disabled(
                                        condition: fn (): bool =>
                                            Auth::guard('web')->check() && Auth::guard('web')->user() instanceof CertifiedCenter
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('certificate_type')
                                    ->label(__('app.certificate_type'))
                                    ->options(CertificateType::class)
                                    ->default(CertificateType::Basic->value)
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('trainee_name')
                                    ->label(__('app.trainee_name'))
                                    ->required()
                                    ->maxLength(255),

                                Select::make('trainer_id')
                                    ->label(__('app.trainer_name'))
                                    ->relationship('trainer', 'name')
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
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label(__('app.phone'))
                                            ->tel()
                                            ->maxLength(255),
                                        Select::make('country_id')
                                            ->label(__('app.country'))
                                            ->relationship('country', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Trainer::create($data)->getKey();
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('country_id')
                                    ->label(__('app.nationality'))
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('app.name'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('code')
                                            ->label(__('app.iso_code_3'))
                                            ->maxLength(3)
                                            ->helperText(__('app.iso_code_3_helper')),
                                        TextInput::make('code_2')
                                            ->label(__('app.iso_code_2'))
                                            ->maxLength(2)
                                            ->helperText(__('app.iso_code_2_helper')),
                                        TextInput::make('nationality')
                                            ->label(__('app.nationality'))
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Country::create($data)->getKey();
                                    }),

                                DatePicker::make('accreditation_date')
                                    ->label(__('app.accreditation_date'))
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),

                Section::make(__('app.document_details_section'))
                    ->description(__('app.document_details_description'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('accredited_serial_number')
                                    ->label(__('app.accredited_serial_number'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('document_code')
                                    ->label(__('app.document_code'))
                                    ->maxLength(255),

                                TextInput::make('accreditation_number')
                                    ->label(__('app.accreditation_number'))
                                    ->maxLength(255)
                                    ->helperText(__('app.will_be_auto_generated')),
                            ]),

                        Select::make('document_type_id')
                            ->label(__('app.document_type'))
                            ->relationship('documentType', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Toggle::make('paper_received')
                            ->label(__('app.paper_document_received'))
                            ->default(false),
                    ]),

                Section::make(__('app.additional_information_section'))
                    ->description(__('app.additional_information_description'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('app.notes'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder(__('app.notes_placeholder')),
                    ])
                    ->collapsible(),
            ]);
    }
}
