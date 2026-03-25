<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/Tables/CenterDocumentTypeRequestsTable.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests\Tables;

use App\Models\CenterDocumentTypeRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CenterDocumentTypeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('app.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('certifiedCenter.name')
                    ->label(__('app.certified_center'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('requested_document_types')
                    ->label(__('app.requested_count'))
                    ->formatStateUsing(fn($state) => count($state ?? [])),

                TextColumn::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->limit(50)
                    ->placeholder(__('app.no_value')),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options([
                        'pending' => __('app.pending'),
                        'approved' => __('app.approved'),
                        'rejected' => __('app.rejected'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
