<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAgentPersons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainerAgentPersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
