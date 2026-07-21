<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Tables;

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\User;
use Carbon\Carbon;
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
                TextColumn::make('creator')
                    ->label(__('app.issued_by'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->creator?->name ?? __('app.unassigned'))
                    ->color(fn ($record) => $record->creator_id ? 'primary' : 'gray'),

                TextColumn::make('assignedTrainer.name')
                    ->label(__('app.assigned_trainer'))
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $record->assignedTrainer?->name ?? __('app.unassigned')),

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

                TextColumn::make('country.name')
                    ->label(__('app.country'))
                    ->searchable(['countries.name'])
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->country_id ? 'info' : 'gray')
                    ->toggleable()
                    ->getStateUsing(fn ($record) => $record->country?->name ?? __('app.unassigned')),

                TextColumn::make('accreditation_date')
                    ->label(__('app.accreditation_date'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return __('app.no_accreditation_date');
                        }
                        try {
                            return Carbon::parse($state)->format('M d, Y');
                        } catch (\Exception $e) {
                            return __('app.invalid_date');
                        }
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

                TextColumn::make('updated_at')
                    ->label(__('app.last_updated'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                SelectFilter::make('creator_type')
                    ->label(__('app.issued_by'))
                    ->options([
                        User::class => __('app.board_admin'),
                        CertifiedCenter::class => __('app.certified_center'),
                        Trainer::class => __('app.trainer'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'])) {
                            return $query;
                        }

                        return $query->where('creator_type', $data['value']);
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

                Filter::make('missing_creator')
                    ->label(__('app.missing_creator'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('creator_id'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('generatePdf')
                    ->label(__('app.certificate_pdf'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {}),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('assignCreator')
                        ->label(__('app.assign_creator'))
                        ->icon('heroicon-o-user')
                        ->color('info')
                        ->form([
                            Select::make('creator_type')
                                ->label(__('app.issued_by'))
                                ->options([
                                    User::class => __('app.board_admin'),
                                    CertifiedCenter::class => __('app.certified_center'),
                                    Trainer::class => __('app.trainer'),
                                ])
                                ->reactive()
                                ->required(),
                            Select::make('creator_id')
                                ->label(__('app.select_creator'))
                                ->options(function (callable $get) {
                                    $type = $get('creator_type');
                                    if (! $type) {
                                        return [];
                                    }

                                    return $type::query()->limit(20)->pluck('name', 'id');
                                })
                                ->getSearchResultsUsing(function (string $search, callable $get) {
                                    $type = $get('creator_type');
                                    if (! $type) {
                                        return [];
                                    }

                                    return $type::query()
                                        ->where('name', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'creator_type' => $data['creator_type'],
                                    'creator_id' => $data['creator_id'],
                                ]);
                            });
                        }),

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
