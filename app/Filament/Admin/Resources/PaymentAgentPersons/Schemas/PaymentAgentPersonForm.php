<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentAgentPersons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentAgentPersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('certified_center_id')
                    ->label(__('app.certified_center'))
                    ->relationship('certifiedCenter', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
