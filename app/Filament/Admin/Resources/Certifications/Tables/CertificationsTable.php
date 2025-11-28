<?php

namespace App\Filament\Admin\Resources\Certifications\Tables;

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
                TextColumn::make('certifiedCenter.name')
                    ->label('Center')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('trainee_name')
                    ->label('Trainee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('certificate_type')
                    ->label('Certificate Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'basic' => 'gray',
                        'advanced' => 'info',
                        'professional' => 'success',
                        'specialist' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'basic' => 'heroicon-o-document',
                        'advanced' => 'heroicon-o-star',
                        'professional' => 'heroicon-o-academic-cap',
                        'specialist' => 'heroicon-o-trophy',
                        default => 'heroicon-o-document',
                    }),

                TextColumn::make('documentType.name')
                    ->label('Document Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-document'),

                TextColumn::make('accredited_serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Serial number copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('accreditation_number')
                    ->label('Accreditation Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Accreditation number copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('document_code')
                    ->label('Document Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('trainer.name')
                    ->label('Trainer')
                    ->searchable(['trainer_name', 'trainer.name'])
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->searchable(['nationality', 'country.name'])
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('accreditation_date')
                    ->label('Accreditation Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('paper_received')
                    ->label('Paper Received')
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
                    ->label('Import Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('certificate_type')
                    ->label('Certificate Type')
                    ->options([
                        'basic' => 'Basic',
                        'advanced' => 'Advanced',
                        'professional' => 'Professional',
                        'specialist' => 'Specialist',
                    ]),

                SelectFilter::make('document_type_id')
                    ->label('Document Type')
                    ->relationship('documentType', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('certified_center_id')
                    ->label('Center')
                    ->relationship('certifiedCenter', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('nationality')
                    ->label('Nationality')
                    ->options(function () {
                        return \App\Models\Certification::whereNotNull('nationality')
                            ->distinct()
                            ->pluck('nationality', 'nationality')
                            ->toArray();
                    }),

                SelectFilter::make('paper_received')
                    ->label('Paper Received')
                    ->options([
                        'YES' => 'Yes',
                        'NO' => 'No',
                        'PENDING' => 'Pending',
                    ]),

                Filter::make('accreditation_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Until Date'),
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

                Filter::make('missing_center')
                    ->label('Missing Center Assignment')
                    ->query(fn (Builder $query): Builder => $query->whereNull('certified_center_id'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('generatePdf')
                    ->label('Certificate PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        // PDF generation logic here:  return response()->streamDownload(...)
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('assignCenter')
                        ->label('Assign Center')
                        ->icon('heroicon-o-building-office')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Select::make('certified_center_id')
                                ->label('Certified Center')
                                ->relationship('certifiedCenter', 'name')
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['certified_center_id' => $data['certified_center_id']]);
                            });
                        }),

                    BulkAction::make('updatePaperStatus')
                        ->label('Update Paper Status')
                        ->icon('heroicon-o-document-check')
                        ->color('warning')
                        ->form([
                            \Filament\Forms\Components\Select::make('paper_received')
                                ->label('Paper Received Status')
                                ->options([
                                    'YES' => 'Yes',
                                    'NO' => 'No',
                                    'PENDING' => 'Pending',
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
