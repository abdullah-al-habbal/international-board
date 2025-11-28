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
                    ->label(__('Key'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('Unique identifier for this document type (e.g., training_certificate)'))
                    ->placeholder('training_certificate'),

                TextInput::make('name.en')
                    ->label(__('Name (English)'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Training Certificate'),

                TextInput::make('name.ar')
                    ->label(__('Name (Arabic)'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder('شهادة تدريب'),
            ]);
    }
}
