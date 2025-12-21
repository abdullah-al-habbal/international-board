<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DocumentTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label(__('app.key'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('app.document_type_key_helper'))
                    ->placeholder('training_certificate'),

                TextInput::make('name.en')
                    ->label(__('app.name_english'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('app.training_certificate_example')),

                TextInput::make('name.ar')
                    ->label(__('app.name_arabic'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder('شهادة تدريب'),
            ]);
    }
}
