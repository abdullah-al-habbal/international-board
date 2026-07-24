<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Certifications\Schemas;

use App\Enums\DocumentTypeRequestStatus;
use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterDocumentType;
use App\Models\Country;
use App\Models\Trainee;
use App\Models\Trainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                Hidden::make('creator_type')
                    ->default(CertifiedCenter::class),

                Hidden::make('creator_id')
                    ->default(fn () => Auth::guard('certified_center')->id()),

                Hidden::make('documentable_type')
                    ->default(CertifiedCenterDocumentType::class),

                Section::make(__('app.certification_details_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('documentable_id')
                                    ->label(__('app.document_type'))
                                    ->options(function () {
                                        $centerId = Auth::guard('certified_center')->id();

                                        return CertifiedCenterDocumentType::where('certified_center_id', $centerId)
                                            ->where('status', DocumentTypeRequestStatus::Approved)
                                            ->get()
                                            ->mapWithKeys(fn ($dt) => [$dt->id => $dt->name[app()->getLocale()] ?? $dt->name['en'] ?? $dt->key]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([

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
                                            ->maxLength(255)
                                            ->unique(Trainee::class, 'name'),
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
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Trainee::create($data)->getKey();
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('country_id')
                                    ->label(__('app.country'))
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
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
                                    ->createOptionUsing(function (array $data): int {
                                        return Country::create($data)->getKey();
                                    }),

                                DatePicker::make('accreditation_date')
                                    ->label(__('app.accreditation_date'))
                                    ->required()
                                    ->default(now()),

                                Select::make('assigned_trainer_id')
                                    ->label(__('app.assigned_trainer'))
                                    ->options(Trainer::where('center_id', Auth::id())->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                            ]),
                    ]),

                Section::make(__('app.document_details_section'))
                    ->schema([
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
