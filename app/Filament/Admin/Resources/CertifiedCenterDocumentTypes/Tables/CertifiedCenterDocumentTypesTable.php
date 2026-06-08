<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Tables;

use App\Enums\DocumentTypeRequestStatus;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CertifiedCenterDocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certifiedCenter.name')
                    ->label(__('app.certified_center'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key')
                    ->label(__('app.document_type_key'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.en')
                    ->label(__('app.name_english'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.ar')
                    ->label(__('app.name_arabic'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray')
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '—'),

                TextColumn::make('created_at')
                    ->label(__('app.assigned_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(DocumentTypeRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('app.approve'))
                    ->visible(fn ($record) => $record->status === DocumentTypeRequestStatus::Pending)
                    ->action(function ($record) {
                        $record->update([
                            'status' => DocumentTypeRequestStatus::Approved,
                            'reviewed_by_admin_id' => Auth::id(),
                        ]);
                    }),

                Action::make('reject')
                    ->label(__('app.reject'))
                    ->visible(fn ($record) => $record->status === DocumentTypeRequestStatus::Pending)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label(__('app.rejection_reason'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => DocumentTypeRequestStatus::Rejected,
                            'admin_notes' => $data['admin_notes'],
                            'reviewed_by_admin_id' => Auth::id(),
                        ]);
                    }),

                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
