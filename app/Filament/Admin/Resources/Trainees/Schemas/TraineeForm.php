<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Schemas;

use App\Models\Country;
use App\Models\Trainee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TraineeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->autofocus()
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('app.email'))
                ->email()
                ->maxLength(255)
                ->nullable()
                ->unique(Trainee::class, 'email', ignoreRecord: true)
                ->columnSpan(1),

            TextInput::make('phone')
                ->label(__('app.phone'))
                ->tel()
                ->maxLength(255)
                ->nullable()
                ->unique(Trainee::class, 'phone', ignoreRecord: true)
                ->columnSpan(1),

            Select::make('country_id')
                ->label(__('app.country'))
                ->searchable()
                ->preload()
                ->nullable()
                ->getSearchResultsUsing(function (string $search): array {
                    $locale = app()->getLocale();

                    return Country::query()
                        ->where(function (Builder $query) use ($search, $locale) {
                            $query->whereRaw(
                                "LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"'))) LIKE ?",
                                ['%'.mb_strtolower($search).'%']
                            );
                        })
                        ->orWhere(function (Builder $query) use ($search) {
                            $query->whereRaw(
                                'LOWER(name) LIKE ?',
                                ['%'.mb_strtolower($search).'%']
                            );
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (Country $country) => [
                            $country->id => $country->getTranslation('name', $locale)
                                ?? $country->getTranslation('name', 'en')
                                ?? $country->name,
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(fn (int $value): string => Country::find($value)?->getTranslation('name', app()->getLocale())
                        ?? Country::find($value)?->getTranslation('name', 'en')
                        ?? ''
                )
                ->createOptionForm([
                    TextInput::make('name')
                        ->label(__('app.name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label(__('app.code'))
                        ->required()
                        ->maxLength(3)
                        ->minLength(3)
                        ->alpha()
                        ->unique(Country::class, 'code')
                        ->helperText(__('app.iso_code_3_helper')),
                    TextInput::make('code_2')
                        ->label(__('app.code_2'))
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
                ->beforeOrEqual('today')
                ->columnSpan(1),

            Select::make('gender')
                ->label(__('app.gender'))
                ->options([
                    'male' => __('app.male'),
                    'female' => __('app.female'),
                ])
                ->nullable()
                ->columnSpan(1),

            Textarea::make('notes')
                ->label(__('app.notes'))
                ->maxLength(65535)
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
        ])->columns(2);
    }
}
