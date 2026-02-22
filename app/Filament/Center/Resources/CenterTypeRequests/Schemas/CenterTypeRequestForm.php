<?php

namespace App\Filament\Center\Resources\CenterTypeRequests\Schemas;

use App\Enums\CenterTypeRequestType;
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
                    ->options(CenterTypeRequestType::class)
                    ->required()
                    ->live(),
                Select::make('document_type_id')
                    ->label(__('app.document_type'))
                    ->relationship('documentType', 'name')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $name = $record->name;
                        if (empty($name)) {
                            $name = $record->getTranslation('name', 'en');
                        }

                        return $name ?: $record->key;
                    })
                    ->visible(fn ($get) => $get('type') === 'course')
                    ->required(fn ($get) => $get('type') === 'course')
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
