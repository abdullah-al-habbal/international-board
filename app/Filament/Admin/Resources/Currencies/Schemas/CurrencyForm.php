<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Currencies\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name.en')
                    ->label(__('app.name_english'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('name.ar')
                    ->label(__('app.name_arabic'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label(__('app.currency_code'))
                    ->required()
                    ->maxLength(3)
                    ->unique(ignoreRecord: true),

                TextInput::make('symbol.en')
                    ->label(__('app.currency_symbol_english'))
                    ->required()
                    ->maxLength(10),

                TextInput::make('symbol.ar')
                    ->label(__('app.currency_symbol_arabic'))
                    ->required()
                    ->maxLength(10),

                Toggle::make('is_default')
                    ->label(__('app.is_default_currency'))
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            Currency::query()
                                ->where('is_default', true)
                                ->update(['is_default' => false]);
                        }
                    }),
            ])->columns(2);
    }
}
