<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Schemas;

use App\Filament\Components\DatePicker;
use App\Models\CertifiedCenter;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('documentable_type')
                    ->default(DocumentType::class),

                Section::make(__('app.import_page.instructions.heading'))
                    ->description(__('app.certification_details_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('creator_type')
                                    ->label(__('app.issued_by'))
                                    ->options([
                                        User::class => __('app.board_admin'),
                                        CertifiedCenter::class => __('app.certified_center'),
                                        Trainer::class => __('app.trainer'),
                                    ])
                                    ->reactive()
                                    ->required(),

                                Select::make('creator_id')
                                    ->label(function (callable $get) {
                                        $type = $get('creator_type');

                                        return match ($type) {
                                            User::class => __('app.board_admin'),
                                            CertifiedCenter::class => __('app.certified_center'),
                                            Trainer::class => __('app.trainer'),
                                            default => __('app.select_creator'),
                                        };
                                    })
                                    ->options(function (callable $get) {
                                        $type = $get('creator_type');
                                        if (! $type) {
                                            return [];
                                        }

                                        return $type::query()->limit(20)->pluck('name', 'id');
                                    })
                                    ->getSearchResultsUsing(function (string $search, callable $get) {
                                        $type = $get('creator_type');
                                        if (! $type) {
                                            return [];
                                        }

                                        return $type::query()
                                            ->where('name', 'like', "%{$search}%")
                                            ->limit(50)
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable(),

                                Select::make('assigned_trainer_id')
                                    ->label(__('app.assigned_trainer'))
                                    ->options(function (callable $get) {
                                        $creatorType = $get('creator_type');
                                        $creatorId = $get('creator_id');
                                        if ($creatorType !== CertifiedCenter::class || ! $creatorId) {
                                            return [];
                                        }

                                        return Trainer::where('center_id', $creatorId)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (callable $get) => $get('creator_type') === CertifiedCenter::class)
                                    ->nullable(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('documentable_id')
                                    ->label(__('app.document_type_name'))
                                    ->options(function () {
                                        return DocumentType::query()
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
                                            ->scopedUnique(
                                                Trainee::class,
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query
                                                    ->where('owner_type', User::class)
                                                    ->where('owner_id', (int) auth('web')->id())
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
                                            'owner_type' => User::class,
                                            'owner_id' => auth('web')->id(),
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
                    ->schema([
                    ]),

                Section::make(__('app.additional_information_section'))
                    ->description(__('app.additional_information_description'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('app.notes'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder(__('app.notes_placeholder')),

                        Toggle::make('show_in_public_website')
                            ->label(__('app.show_in_public_website'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
