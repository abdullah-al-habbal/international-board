<?php

namespace App\Filament\Admin\Resources\EditRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EditRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('editable_type')
                    ->label(__('app.editable_type')),
                TextEntry::make('editable_id')
                    ->label(__('app.editable_id')),
                TextEntry::make('status')
                    ->label(__('app.status'))
                    ->badge(),
                TextEntry::make('rejection_reason')
                    ->label(__('app.rejection_reason'))
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime(),
            ]);
    }
}
