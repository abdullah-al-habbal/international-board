<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DocumentTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DocumentTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key')
                    ->label(__('app.key'))
                    ->badge()
                    ->color('primary'),

                TextEntry::make('name_en')
                    ->label(__('app.name_english'))
                    ->state(fn ($record) => $record->getTranslation('name', 'en')),

                TextEntry::make('name_ar')
                    ->label(__('app.name_arabic'))
                    ->state(fn ($record) => $record->getTranslation('name', 'ar')),

                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
