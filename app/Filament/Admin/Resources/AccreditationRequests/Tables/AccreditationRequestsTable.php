<?php
// filePath: app/Filament/Admin/Resources/AccreditationRequests/Tables/AccreditationRequestsTable.php
declare(strict_types=1);

namespace App\Filament\Admin\Resources\AccreditationRequests\Tables;

use App\Enums\AccreditationStatus;
use App\Enums\CenterStatus;
use App\Models\AccreditationRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccreditationRequestsTable
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

                TextColumn::make('requested_start_date')
                    ->label(__('app.requested_start_date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('requested_end_date')
                    ->label(__('app.requested_end_date'))
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
                    ->visible(fn(AccreditationRequest $record) => $record->status !== AccreditationStatus::Approved)
                    ->action(function (AccreditationRequest $record): void {
                        $record->update([
                            'status' => AccreditationStatus::Approved,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $center = $record->certifiedCenter;
                        if ($center) {
                            $center->update([
                                'accreditation_period_start' => $record->requested_start_date,
                                'accreditation_period_end' => $record->requested_end_date,
                                'status' => CenterStatus::Active,
                                'is_active' => true,
                            ]);
                        }
                    }),

                Action::make('reject')
                    ->label(__('app.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(AccreditationRequest $record) => $record->status !== AccreditationStatus::Rejected)
                    ->action(function (AccreditationRequest $record): void {
                        $record->update([
                            'status' => AccreditationStatus::Rejected,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $center = $record->certifiedCenter;
                        if (!$center) {
                            return;
                        }

                        $hasOtherActive = $center->accreditationRequests()
                            ->where('id', '!=', $record->id)
                            ->where('status', AccreditationStatus::Approved)
                            ->exists();

                        if (!$hasOtherActive) {
                            $center->update([
                                'status' => CenterStatus::Suspended,
                                'is_active' => false,
                            ]);
                        }
                    }),

                Action::make('under_review')
                    ->label(__('app.under_review'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn(AccreditationRequest $record) => $record->status !== AccreditationStatus::UnderReview)
                    ->action(function (AccreditationRequest $record): void {
                        $record->update([
                            'status' => AccreditationStatus::UnderReview,
                            'reviewed_by' => auth()->id(),
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
