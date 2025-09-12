<?php

namespace App\Filament\Admin\Resources\Certifications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('certifiedCenter.name')
                    ->label('Certified center'),
                TextEntry::make('certificate_type')
                    ->badge(),
                TextEntry::make('trainee_name'),
                TextEntry::make('accredited_serial_number'),
                TextEntry::make('document_code'),
                TextEntry::make('document_type')
                    ->badge(),
                TextEntry::make('accreditation_date')
                    ->date(),
                TextEntry::make('trainer.name')
                    ->label('Trainer'),
                TextEntry::make('country.name')
                    ->label('Country'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
