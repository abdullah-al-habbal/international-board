<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Tables;

use App\Enums\DocumentTypeRequestStatus;
use App\Models\DocumentType;
use App\Models\TrainerDocumentType;
use App\Models\TrainerDocumentTypeRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TrainerDocumentTypeRequestsTable
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

                TextColumn::make('trainer.name')
                    ->label(__('app.trainer'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (DocumentTypeRequestStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('requested_document_types')
                    ->label(__('app.requested_types'))
                    ->formatStateUsing(function ($state) {
                        return DocumentType::whereIn('id', $state ?? [])->pluck('name')->implode(', ');
                    }),

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
                EditAction::make()
                    ->visible(fn (TrainerDocumentTypeRequest $record): bool => $record->status === DocumentTypeRequestStatus::Pending),

                Action::make('approve')
                    ->label(__('app.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (TrainerDocumentTypeRequest $record): bool => $record->status === DocumentTypeRequestStatus::Pending)
                    ->action(function (TrainerDocumentTypeRequest $record): void {
                        DB::transaction(function () use ($record): void {
                            $record->update([
                                'status' => DocumentTypeRequestStatus::Approved,
                            ]);

                            foreach ($record->requested_document_types ?? [] as $docTypeId) {
                                TrainerDocumentType::firstOrCreate([
                                    'trainer_id' => $record->trainer_id,
                                    'document_type_id' => $docTypeId,
                                ]);
                            }
                        });

                        Notification::make()
                            ->title(__('app.document_type_request_approved'))
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label(__('app.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn (TrainerDocumentTypeRequest $record): bool => $record->status === DocumentTypeRequestStatus::Pending)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label(__('app.admin_notes'))
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (TrainerDocumentTypeRequest $record, array $data): void {
                        $record->update([
                            'status' => DocumentTypeRequestStatus::Rejected,
                            'admin_notes' => $data['admin_notes'],
                        ]);

                        Notification::make()
                            ->title(__('app.document_type_request_rejected'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
