<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Tables;

use App\Filament\Components\DatePicker;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
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

                TextColumn::make('documentable.name')
                    ->label(__('app.document_type'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('app.unassigned')),

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

                IconColumn::make('show_in_public_website')
                    ->label(__('app.show_in_public_website'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
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
                DeleteAction::make(),
            ])

            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
