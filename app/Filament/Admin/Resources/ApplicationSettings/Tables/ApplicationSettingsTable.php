<?php

// app\Filament\Admin\Resources\ApplicationSettings\Tables\ApplicationSettingsTable.php

namespace App\Filament\Admin\Resources\ApplicationSettings\Tables;

use App\Enums\SettingType;
use Filament\Actions\BulkActionGroup;
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
                    ->label(__('app.key'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('value')
                    ->label(__('app.value'))
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $value = $record->getTypedValue();

                        if (is_bool($value)) {
                            return $value ? __('app.yes') : __('app.no');
                        }

                        if (is_array($value)) {
                            return json_encode($value, JSON_UNESCAPED_UNICODE);
                        }

                        return $value ?: __('app.no_value');
                    })
                    ->limit(50),

                TextColumn::make('type')
                    ->label(__('app.type'))
                    ->badge()
                    ->formatStateUsing(fn (?SettingType $state): string => $state?->label() ?? '')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
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
