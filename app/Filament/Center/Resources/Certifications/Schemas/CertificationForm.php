<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Certifications\Schemas;

use App\Models\Country;
use App\Models\Trainee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.certification_details_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Select::make('document_type_id')
                                    ->label(__('app.document_type'))
                                    ->relationship('documentType', 'name')
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $name = $record->name;
                                        if (empty($name)) {
                                            $name = $record->getTranslation('name', app()->getLocale());
                                        }

                                        return $name ?: $record->key;
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('app.select_document_type')),

                                Select::make('trainee_id')
                                    ->label(__('app.trainee_name'))
                                    ->relationship('trainee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
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
                                        DatePicker::make('date_of_birth')
                                            ->label(__('app.date_of_birth')),
                                        TextInput::make('nationality')
                                            ->label(__('app.nationality'))
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Trainee::create($data)->getKey();
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
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('code')
                                            ->maxLength(3)
                                            ->helperText(__('app.iso_code_3_helper')),
                                        TextInput::make('code_2')
                                            ->maxLength(2)
                                            ->helperText(__('app.iso_code_2_helper')),
                                        TextInput::make('nationality')
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
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextInput::make('document_code')
                                    ->label(__('app.document_code'))
                                    ->maxLength(255),
                            ]),

                        Toggle::make('paper_received')
                            ->label(__('app.paper_document_received'))
                            ->default(false),
                    ]),

                Section::make(__('app.additional_information_section'))
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
