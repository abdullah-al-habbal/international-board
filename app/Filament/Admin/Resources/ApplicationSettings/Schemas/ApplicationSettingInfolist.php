<?php

// app\Filament\Admin\Resources\ApplicationSettings\Schemas\ApplicationSettingInfolist.php

namespace App\Filament\Admin\Resources\ApplicationSettings\Schemas;

use App\Enums\SettingType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ApplicationSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key')
                    ->label('Key')
                    ->copyable(),

                TextEntry::make('value')
                    ->label('Value')
                    ->formatStateUsing(function ($record) {
                        $value = $record->getTypedValue();

                        if (is_bool($value)) {
                            return $value ? 'Yes' : 'No';
                        }

                        if (is_array($value)) {
                            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $value;
                    }),

                TextEntry::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?SettingType $state): string => $state?->label() ?? ''),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}
