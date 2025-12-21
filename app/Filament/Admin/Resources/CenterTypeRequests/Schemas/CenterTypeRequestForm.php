<?php

namespace App\Filament\Admin\Resources\CenterTypeRequests\Schemas;

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
                Select::make('certified_center_id')
                    ->label(__('app.certified_center'))
                    ->relationship('center', 'name')
                    ->required(),
                Select::make('type')
                    ->label(__('app.request_type'))
                    ->options(\App\Enums\CenterTypeRequestType::class)
                    ->required(),
                Select::make('document_type_id')
                    ->label(__('app.document_type'))
                    ->relationship('documentType', 'name')
                    ->visible(fn($get) => $get('type') === 'course')
                    ->required(fn($get) => $get('type') === 'course'),
                TextInput::make('requested_name')
                    ->label(__('app.requested_name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('requested_description')
                    ->label(__('app.requested_description'))
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('app.status'))
                    ->options(\App\Enums\CenterTypeRequestStatus::class)
                    ->required(),
                Textarea::make('rejection_message')
                    ->label(__('app.rejection_message'))
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn($record) => $record && $record->status === \App\Enums\CenterTypeRequestStatus::Rejected),
            ]);
    }
}
