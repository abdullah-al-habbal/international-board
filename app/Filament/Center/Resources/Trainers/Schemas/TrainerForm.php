<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([
            TextInput::make('name')
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('app.email'))
                ->email()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable(),

            TextInput::make('phone')
                ->label(__('app.phone'))
                ->tel()
                ->maxLength(255)
                ->nullable(),

            Select::make('country_id')
                ->label(__('app.country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            FileUpload::make('avatar')
                ->label(__('app.avatar'))
                ->image()
                ->avatar()
                ->directory('trainers/avatars')
                ->visibility('public')
                ->nullable(),

            Select::make('specializations')
                ->relationship('specializations', 'name')
                ->label(__('app.specializations'))
                ->multiple()
                ->searchable()
                ->preload()
                ->nullable()
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label(__('app.active'))
                ->default(true)
                ->inline(false),
        ]);
    }
}
