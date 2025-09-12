<?php

namespace App\Filament\Admin\Resources\AccreditationRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AccreditationRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('certifiedCenter.name')
                    ->label('Certified center'),
                TextEntry::make('requested_start_date')
                    ->dateTime(),
                TextEntry::make('requested_end_date')
                    ->dateTime(),
                TextEntry::make('request_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('admin_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('reviewed_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('reviewed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
