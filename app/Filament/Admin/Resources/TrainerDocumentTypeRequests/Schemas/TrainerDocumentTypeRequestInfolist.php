<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Schemas;

use App\Enums\DocumentTypeRequestStatus;
use App\Models\DocumentType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TrainerDocumentTypeRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('trainer.name')
                    ->label(__('app.trainer')),

                TextEntry::make('status')
                    ->label(__('app.status'))
                    ->badge(),

                TextEntry::make('requested_document_types')
                    ->label(__('app.requested_document_types'))
                    ->formatStateUsing(function ($state) {
                        return DocumentType::whereIn('id', $state ?? [])
                            ->pluck('name')
                            ->implode(', ');
                    }),

                TextEntry::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->columnSpanFull()
                    ->placeholder(__('app.no_value'))
                    ->visible(fn ($record) => $record->status === DocumentTypeRequestStatus::Rejected),

                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime(),
            ]);
    }
}
