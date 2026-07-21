<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Certifications\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trainee.name')
                    ->label(__('app.trainee'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->getStateUsing(fn ($record) => $record->trainee?->name ?? __('app.unassigned')),

                TextColumn::make('accredited_serial_number')
                    ->label(__('app.serial_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('app.serial_copied'))
                    ->copyMessageDuration(1500),

                TextColumn::make('document_code')
                    ->label(__('app.document_code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('assignedTrainer.name')
                    ->label(__('app.assigned_trainer'))
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $record->assignedTrainer?->name ?? __('app.unassigned')),

                TextColumn::make('country.name')
                    ->label(__('app.country'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->getStateUsing(fn ($record) => $record->country?->name ?? __('app.unassigned')),

                TextColumn::make('accreditation_date')
                    ->label(__('app.accreditation_date'))
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        if (empty($record->accreditation_date)) {
                            return __('app.no_accreditation_date');
                        }

                        return $record->accreditation_date;
                    }),

                IconColumn::make('paper_received')
                    ->label(__('app.paper_received'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(function ($record) {
                        $value = strtoupper($record->paper_received ?? '');

                        return $value === 'YES' || $value === 'YAS';
                    })
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('app.import_date'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('paper_received')
                    ->label(__('app.paper_received'))
                    ->options([
                        'YES' => __('app.yes'),
                        'NO' => __('app.no'),
                        'PENDING' => __('app.pending'),
                    ]),

                Filter::make('accreditation_date')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('app.from_date')),
                        DatePicker::make('until')
                            ->label(__('app.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('accreditation_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('accreditation_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('generatePdf')
                    ->label(__('app.certificate_pdf'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {}),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('updatePaperStatus')
                        ->label(__('app.update_paper_status'))
                        ->icon('heroicon-o-document-check')
                        ->color('warning')
                        ->form([
                            Select::make('paper_received')
                                ->label(__('app.paper_received_status'))
                                ->options([
                                    'YES' => __('app.yes'),
                                    'NO' => __('app.no'),
                                    'PENDING' => __('app.pending'),
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['paper_received' => $data['paper_received']]);
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
