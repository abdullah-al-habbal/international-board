<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainerRoleForm
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
            ])->columns(2);
    }
}
