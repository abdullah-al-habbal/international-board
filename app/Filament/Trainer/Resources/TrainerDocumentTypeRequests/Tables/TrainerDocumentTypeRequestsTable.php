<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Tables;

use App\Enums\DocumentTypeRequestStatus;
use App\Models\DocumentType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainerDocumentTypeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label(__('app.date'))->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (DocumentTypeRequestStatus $state): string => $state->color()),
                TextColumn::make('requested_document_types')
                    ->label(__('app.requested_types'))
                    ->formatStateUsing(function ($state) {
                        return DocumentType::whereIn('id', $state)->pluck('name')->implode(', ');
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status === DocumentTypeRequestStatus::Pending),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
