<?php
// app\Filament\Admin\Resources\ApplicationSettings\Tables\ApplicationSettingsTable.php
namespace App\Filament\Admin\Resources\ApplicationSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $value = $record->getTypedValue();

                        if (is_bool($value)) {
                            return $value ? 'Yes' : 'No';
                        }

                        if (is_array($value)) {
                            return json_encode($value);
                        }

                        return $value ?: 'No value';
                    })
                    ->limit(50),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
