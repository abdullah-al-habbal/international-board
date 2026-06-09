<?php

namespace App\Filament\Trainer\Resources\Certifications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([
            Section::make(__('app.certification_details'))
                ->schema([
                    TextEntry::make('creator.name')
                        ->label(__('app.created_by')),

                    TextEntry::make('trainee.name')
                        ->label(__('app.trainee')),

                    TextEntry::make('country.name')
                        ->label(__('app.country')),

                    TextEntry::make('accredited_serial_number')
                        ->label(__('app.accredited_serial_number')),

                    TextEntry::make('document_code')
                        ->label(__('app.document_code')),

                    TextEntry::make('accreditation_number')
                        ->label(__('app.accreditation_number')),

                    TextEntry::make('accreditation_date')
                        ->label(__('app.accreditation_date'))
                        ->date(),

                    TextEntry::make('paper_received')
                        ->label(__('app.paper_received')),

                    TextEntry::make('notes')
                        ->label(__('app.notes'))
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
