<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Tables;

use App\Models\Certification;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                // Certified Center with NULL handling
                TextColumn::make('certifiedCenter.name')
                    ->label(__('app.center'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->getStateUsing(fn($record) => $record->certifiedCenter?->name ?? __('app.unassigned'))
                    ->color(fn($record) => $record->certified_center_id ? 'primary' : 'gray'),

                TextColumn::make('trainee.name')
                    ->label(__('app.trainee'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->getStateUsing(fn($record) => $record->trainee?->name ?? __('app.unassigned')),

                TextColumn::make('nationality')
                    ->label(__('app.nationality'))
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                // REMOVED: certificate_type column
                // Now using documentType.name exclusively

                // Document Type with NULL handling
                TextColumn::make('documentType.name')
                    ->label(__('app.document_type'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->document_type_id ? 'info' : 'gray')
                    ->icon('heroicon-o-document')
                    ->getStateUsing(function ($record) {
                        if (!$record->documentType) {
                            return __('app.no_document_type');
                        }

                        $name = $record->documentType->name;
                        if (empty($name)) {
                            $name = $record->documentType->getTranslation('name', app()->getLocale());
                        }

                        return $name ?: $record->documentType->key;
                    }),

                TextColumn::make('accredited_serial_number')
                    ->label(__('app.serial_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('app.serial_copied'))
                    ->copyMessageDuration(1500),

                TextColumn::make('accreditation_number')
                    ->label(__('app.accreditation_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('app.accreditation_copied'))
                    ->copyMessageDuration(1500),

                TextColumn::make('document_code')
                    ->label(__('app.document_code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // Trainer with NULL handling
                TextColumn::make('trainer.name')
                    ->label(__('app.trainer'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(fn($record) => $record->trainer_id ? 'warning' : 'gray')
                    ->getStateUsing(fn($record) => $record->trainer?->name ?? __('app.unassigned')),

                // Country with NULL handling
                TextColumn::make('country.name')
                    ->label(__('app.country'))
                    ->searchable(['countries.name'])
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->country_id ? 'info' : 'gray')
                    ->toggleable()
                    ->getStateUsing(fn($record) => $record->country?->name ?? __('app.unassigned')),

                TextColumn::make('accreditation_date')
                    ->label(__('app.accreditation_date'))
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

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

                TextColumn::make('updated_at')
                    ->label(__('app.last_updated'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // REMOVED: certificate_type filter
                // Now using document_type_id filter

                SelectFilter::make('document_type_id')
                    ->label(__('app.document_type'))
                    ->relationship('documentType', 'name')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $name = $record->name;
                        if (empty($name)) {
                            $name = $record->getTranslation('name', app()->getLocale());
                        }

                        return $name ?: $record->key;
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('certified_center_id')
                    ->label(__('app.center'))
                    ->relationship('certifiedCenter', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('nationality')
                    ->label(__('app.nationality'))
                    ->options(function () {
                        return Certification::whereNotNull('nationality')
                            ->distinct()
                            ->pluck('nationality', 'nationality')
                            ->toArray();
                    }),

                SelectFilter::make('paper_received')
                    ->label(__('app.paper_received'))
                    ->options([
                        'YES' => __('app.yes'),
                        'NO' => __('app.no'),
                        'PENDING' => __('app.pending'),
                    ]),

                Filter::make('accreditation_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label(__('app.from_date')),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label(__('app.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('accreditation_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('accreditation_date', '<=', $date),
                            );
                    }),

                Filter::make('missing_center')
                    ->label(__('app.missing_center'))
                    ->query(fn(Builder $query): Builder => $query->whereNull('certified_center_id'))
                    ->toggle(),

                Filter::make('missing_document_type')
                    ->label(__('app.missing_document_type'))
                    ->query(fn(Builder $query): Builder => $query->whereNull('document_type_id'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('generatePdf')
                    ->label(__('app.certificate_pdf'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        // PDF generation logic here
                        // return response()->streamDownload(...)
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('assignCenter')
                        ->label(__('app.assign_center'))
                        ->icon('heroicon-o-building-office')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('certified_center_id')
                                ->label(__('app.certified_center'))
                                ->relationship('certifiedCenter', 'name')
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['certified_center_id' => $data['certified_center_id']]);
                            });
                        }),

                    BulkAction::make('assignDocumentType')
                        ->label(__('app.assign_document_type'))
                        ->icon('heroicon-o-document')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('document_type_id')
                                ->label(__('app.document_type'))
                                ->relationship('documentType', 'name')
                                ->getOptionLabelFromRecordUsing(function ($record) {
                                    $name = $record->name;
                                    if (empty($name)) {
                                        $name = $record->getTranslation('name', app()->getLocale());
                                    }

                                    return $name ?: $record->key;
                                })
                                ->required()
                                ->searchable()
                                ->preload(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['document_type_id' => $data['document_type_id']]);
                            });
                        }),

                    BulkAction::make('updatePaperStatus')
                        ->label(__('app.update_paper_status'))
                        ->icon('heroicon-o-document-check')
                        ->color('warning')
                        ->form([
                            \Filament\Forms\Components\Select::make('paper_received')
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
