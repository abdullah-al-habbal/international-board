<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\Certifications\Schemas;

use App\Enums\DocumentTypeRequestStatus;
use App\Filament\Components\DatePicker;
use App\Models\Country;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\TrainerDocumentType;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('creator_type')
                    ->default(Trainer::class),

                Hidden::make('creator_id')
                    ->default(fn () => Auth::guard('trainer')->id()),

                Hidden::make('documentable_type')
                    ->default(TrainerDocumentType::class),

                Section::make(__('app.certification_details_section'))
                    ->description(__('app.certification_details_description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('documentable_id')
                                    ->label(__('app.document_type'))
                                    ->options(function () {
                                        $trainerId = Auth::guard('trainer')->id();

                                        return TrainerDocumentType::where('trainer_id', $trainerId)
                                            ->where('status', DocumentTypeRequestStatus::Approved)
                                            ->get()
                                            ->mapWithKeys(fn ($dt) => [$dt->id => $dt->name[app()->getLocale()] ?? $dt->name['en'] ?? $dt->key]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('trainee_id')
                                    ->label(__('app.trainee_name'))
                                    ->relationship(
                                        'trainee',
                                        'name',
                                        fn (Builder $query) => $query
                                            ->where('owner_type', Trainer::class)
                                            ->where('owner_id', (int) auth('trainer')->id())
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('app.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->scopedUnique(
                                                Trainee::class,
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query
                                                    ->where('owner_type', Trainer::class)
                                                    ->where('owner_id', (int) auth('trainer')->id())
                                            ),
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
                                        return Trainee::create($data + [
                                            'owner_type' => Trainer::class,
                                            'owner_id' => auth('trainer')->id(),
                                        ])->getKey();
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
                            ]),
                    ]),

                Section::make(__('app.document_details_section'))
                    ->description(__('app.document_details_description'))
                    ->schema([]),

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
