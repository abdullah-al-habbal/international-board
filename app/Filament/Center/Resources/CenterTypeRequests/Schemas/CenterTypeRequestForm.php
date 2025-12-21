<?php

namespace App\Filament\Center\Resources\CenterTypeRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CenterTypeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('app.request_type'))
                    ->options(\App\Enums\CenterTypeRequestType::class)
                    ->required()
                    ->live(),
                Select::make('document_type_id')
                    ->label(__('app.document_type'))
                    ->relationship('documentType', 'name')
                    ->visible(fn($get) => $get('type') === 'course')
                    ->required(fn($get) => $get('type') === 'course')
                    ->searchable()
                    ->preload(),
                TextInput::make('requested_name')
                    ->label(__('app.requested_name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('requested_description')
                    ->label(__('app.requested_description'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
