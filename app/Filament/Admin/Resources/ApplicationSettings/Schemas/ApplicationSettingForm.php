<?php
// app\Filament\Admin\Resources\ApplicationSettings\Schemas\ApplicationSettingForm.php
namespace App\Filament\Admin\Resources\ApplicationSettings\Schemas;

use App\Enums\SettingType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class ApplicationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Key')
                    ->helperText('Use only English letters and underscores (e.g., site_title)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash']),

                Select::make('type')
                    ->label('Type')
                    ->options(
                        collect(SettingType::cases())
                            ->mapWithKeys(fn($case) => [
                                $case->value => ucfirst($case->value)
                            ])
                    )
                    ->default(SettingType::Text->value)
                    ->live()
                    ->required(),

                TextInput::make('value')
                    ->label('Value')
                    ->visible(fn($get) => in_array($get('type'), [SettingType::Text->value, SettingType::Email->value, SettingType::Url->value]))
                    ->required(),

                TextInput::make('value')
                    ->label('Value')
                    ->numeric()
                    ->visible(fn($get) => $get('type') === SettingType::Number->value)
                    ->required(),

                Toggle::make('value')
                    ->label('Value')
                    ->visible(fn($get) => $get('type') === SettingType::Boolean->value)
                    ->required(),

                Textarea::make('value')
                    ->label('Value')
                    ->visible(fn($get) => $get('type') === SettingType::Json->value)
                    ->rules(['json'])
                    ->required(),
            ]);
    }
}
