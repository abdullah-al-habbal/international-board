<?php

namespace App\Filament\Trainer\Resources\Certifications\Tables;

use App\Models\Certification;
use App\Models\Trainer;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CertificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(self::getTableQuery())
            ->columns(self::getColumns())
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                ViewAction::make()
                    ->icon(Heroicon::OutlinedEye),
                EditAction::make()
                    ->icon(Heroicon::OutlinedPencilSquare),
            ])
            ->bulkActions([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('app.create_certification'))
                    ->icon(Heroicon::OutlinedPlus),
            ]);
    }

    private static function getTableQuery(): Builder
    {
        return Certification::query()
            ->where('creator_type', Trainer::class)
            ->where('creator_id', Auth::guard('trainer')->id())
            ->with([
                'trainee',
                'country',
                'creator',
            ]);
    }

    private static function getColumns(): array
    {
        return [
            TextColumn::make('documentable.name')
                ->label(__('app.document_type'))
                ->searchable()
                ->sortable(),

            TextColumn::make('accredited_serial_number')
                ->label(__('app.accredited_serial_number'))
                ->searchable()
                ->sortable(),

            TextColumn::make('trainee.name')
                ->label(__('app.trainee_name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('country.name')
                ->label(__('app.country'))
                ->sortable(),

            TextColumn::make('document_code')
                ->label(__('app.document_code'))
                ->searchable()
                ->sortable(),

            TextColumn::make('accreditation_date')
                ->label(__('app.accreditation_date'))
                ->date()
                ->sortable(),

            TextColumn::make('creator.name')
                ->label(__('app.created_by'))
                ->sortable(),

            TextColumn::make('created_at')
                ->label(__('app.created_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
