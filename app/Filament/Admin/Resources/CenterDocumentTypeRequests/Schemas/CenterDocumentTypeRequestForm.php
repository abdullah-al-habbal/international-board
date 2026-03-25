<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/Forms/CenterDocumentTypeRequestForm.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests\Forms;

use App\Models\DocumentType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CenterDocumentTypeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('certified_center_id')
                    ->label(__('app.certified_center'))
                    ->relationship('certifiedCenter', 'name')
                    ->required()
                    ->searchable(),

                Repeater::make('requested_document_types')
                    ->label(__('app.requested_document_types'))
                    ->schema([
                        Select::make('document_type_id')
                            ->label(__('app.document_type'))
                            ->options(DocumentType::pluck('name->en', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('status')
                            ->label(__('app.status'))
                            ->options([
                                'pending' => __('app.pending'),
                                'approved' => __('app.approved'),
                                'rejected' => __('app.rejected'),
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2)
                    ->required()
                    ->minItems(1)
                    ->maxItems(20),

                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->columnSpanFull(),
            ]);
    }
}
