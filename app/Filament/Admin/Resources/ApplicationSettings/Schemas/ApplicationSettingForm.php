<?php

// app\Filament\Admin\Resources\ApplicationSettings\Schemas\ApplicationSettingForm.php

namespace App\Filament\Admin\Resources\ApplicationSettings\Schemas;

use App\Enums\SettingType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApplicationSettingForm
{
    private static function resolveType(mixed $type): ?string
    {
        return $type instanceof SettingType ? $type->value : $type;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label(__('app.key'))
                    ->helperText(__('app.key_helper'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash'])
                    ->disabled(fn ($operation) => $operation === 'edit')
                    ->dehydrated(fn ($state, $operation) => $operation === 'create'),

                Select::make('type')
                    ->label(__('app.type'))
                    ->options(
                        collect(SettingType::cases())
                            ->mapWithKeys(fn (SettingType $case) => [
                                $case->value => $case->label(),
                            ])
                    )
                    ->default(SettingType::Text->value)
                    ->live()
                    ->required()
                    ->disabled(fn ($operation) => $operation === 'edit'),

                TextInput::make('value')
                    ->label(__('app.value'))
                    ->visible(fn ($get) => in_array(self::resolveType($get('type')), [SettingType::Text->value, SettingType::Email->value, SettingType::Phone->value, SettingType::Url->value]))
                    ->required(fn ($get) => in_array(self::resolveType($get('type')), [SettingType::Text->value, SettingType::Email->value, SettingType::Phone->value, SettingType::Url->value])),

                TextInput::make('value')
                    ->label(__('app.value'))
                    ->numeric()
                    ->visible(fn ($get) => self::resolveType($get('type')) === SettingType::Number->value)
                    ->required(fn ($get) => self::resolveType($get('type')) === SettingType::Number->value),

                Toggle::make('value')
                    ->label(__('app.value'))
                    ->visible(fn ($get) => self::resolveType($get('type')) === SettingType::Boolean->value)
                    ->required(fn ($get) => self::resolveType($get('type')) === SettingType::Boolean->value),

                Textarea::make('value')
                    ->label(__('app.value'))
                    ->visible(fn ($get) => in_array(self::resolveType($get('type')), [SettingType::Json->value, SettingType::Html->value]))
                    ->rules(fn ($get) => self::resolveType($get('type')) === SettingType::Json->value ? ['json'] : [])
                    ->required(fn ($get) => in_array(self::resolveType($get('type')), [SettingType::Json->value, SettingType::Html->value])),
            ]);
    }
}
