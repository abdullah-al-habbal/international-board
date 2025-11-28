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
                    ->label(__('Key'))
                    ->badge()
                    ->color('primary'),

                TextEntry::make('name.en')
                    ->label(__('Name (English)')),

                TextEntry::make('name.ar')
                    ->label(__('Name (Arabic)')),

                TextEntry::make('certifications_count')
                    ->label(__('Certifications Count'))
                    ->counts('certifications')
                    ->badge()
                    ->color('success'),

                TextEntry::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
