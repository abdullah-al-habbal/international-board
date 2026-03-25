<?php

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertifiedCenterDocumentTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('certifiedCenter.name')
                    ->label('Certified center'),
                TextEntry::make('documentType.name')
                    ->label('Document type'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
