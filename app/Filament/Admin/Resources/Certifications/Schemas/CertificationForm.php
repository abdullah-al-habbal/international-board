<?php

namespace App\Filament\Admin\Resources\Certifications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.import_page.instructions.heading'))
                    ->description('Main certification details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('certified_center_id')
                                    ->label(__('app.certified_center'))
                                    ->relationship('certifiedCenter', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('certificate_type')
                                    ->label(__('app.certificate_type'))
                                    ->options([
                                        'basic' => 'Basic',
                                        'advanced' => 'Advanced',
                                        'professional' => 'Professional',
                                        'specialist' => 'Specialist',
                                    ])
                                    ->default('basic')
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
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->maxLength(255),
                                        Select::make('country_id')
                                            ->relationship('country', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return \App\Models\Trainer::create($data)->getKey();
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
                                            ->helperText('ISO 3166-1 alpha-3 code'),
                                        TextInput::make('code_2')
                                            ->maxLength(2)
                                            ->helperText('ISO 3166-1 alpha-2 code'),
                                        TextInput::make('nationality')
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return \App\Models\Country::create($data)->getKey();
                                    }),

                                DatePicker::make('accreditation_date')
                                    ->label(__('app.accreditation_date'))
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),

                Section::make('Document Details')
                    ->description('Certificate and document information')
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

                        Select::make('paper_received')
                            ->label(__('app.paper_document_received'))
                            ->options([
                                'YES' => __('app.yes'),
                                'NO' => __('app.no'),
                                'PENDING' => __('app.pending'),
                            ])
                            ->default('NO'),
                    ]),

                Section::make('Additional Information')
                    ->description('Notes and additional details')
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('app.notes'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Additional notes or comments...'),
                    ])
                    ->collapsible(),
            ]);
    }
}
