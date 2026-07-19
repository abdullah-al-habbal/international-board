<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterAccreditationRequests\Tables;

use App\Enums\AccreditationStatus;
use App\Models\CenterAccreditationRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CenterAccreditationRequestsTable
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

                TextColumn::make('accreditation_start_date')
                    ->label(__('app.accreditation_start_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('accreditation_end_date')
                    ->label(__('app.accreditation_end_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label(__('app.reviewed_by'))
                    ->placeholder(__('app.no_value'))
                    ->sortable(),

                TextColumn::make('reviewed_at')
                    ->label(__('app.reviewed_at'))
                    ->dateTime()
                    ->placeholder(__('app.no_value'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options(AccreditationStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('approve')
                    ->label(__('app.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        DateTimePicker::make('accreditation_end_date')
                            ->label(__('app.accreditation_end_date'))
                            ->required()
                            ->after(fn () => now()),
                    ])
                    ->visible(fn (CenterAccreditationRequest $record) => $record->status !== AccreditationStatus::Approved)
                    ->action(function (CenterAccreditationRequest $record, array $data): void {
                        $record->update([
                            'status' => AccreditationStatus::Approved->value,
                            'accreditation_start_date' => now(),
                            'accreditation_end_date' => $data['accreditation_end_date'],
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),

                Action::make('reject')
                    ->label(__('app.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CenterAccreditationRequest $record) => $record->status !== AccreditationStatus::Rejected)
                    ->action(function (CenterAccreditationRequest $record): void {
                        $record->update([
                            'status' => AccreditationStatus::Rejected->value,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),

                Action::make('under_review')
                    ->label(__('app.under_review'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (CenterAccreditationRequest $record) => $record->status !== AccreditationStatus::UnderReview)
                    ->action(function (CenterAccreditationRequest $record): void {
                        $record->update([
                            'status' => AccreditationStatus::UnderReview->value,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
