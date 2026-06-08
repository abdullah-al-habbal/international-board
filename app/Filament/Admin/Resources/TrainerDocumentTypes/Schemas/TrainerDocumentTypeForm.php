<?php

namespace App\Filament\Admin\Resources\TrainerDocumentTypes\Schemas;

use App\Enums\DocumentTypeRequestStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainerDocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trainer_id')
                    ->relationship('trainer', 'name')
                    ->required(),

                TextInput::make('key')
                    ->label(__('app.key'))
                    ->required()
                    ->maxLength(255)
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

                Select::make('status')
                    ->label(__('app.status'))
                    ->options(collect(DocumentTypeRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->default(DocumentTypeRequestStatus::Pending->value)
                    ->required(),
            ]);
    }
}
