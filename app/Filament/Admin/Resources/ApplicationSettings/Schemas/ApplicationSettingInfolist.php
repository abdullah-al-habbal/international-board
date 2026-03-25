<?php
// app\Filament\Admin\Resources\ApplicationSettings\Schemas\ApplicationSettingInfolist.php
namespace App\Filament\Admin\Resources\ApplicationSettings\Schemas;

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
                            return json_encode($value, JSON_PRETTY_PRINT);
                        }

                        return $value;
                    }),

                TextEntry::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state)),

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
