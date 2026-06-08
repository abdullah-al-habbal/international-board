<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CenterAccreditationRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make(__('app.request_details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('certifiedCenter.name')
                            ->label(__('app.certified_center'))
                            ->columnSpanFull(),

                        TextEntry::make('accreditation_start_date')
                            ->label(__('app.accreditation_start_date'))
                            ->dateTime(),

                        TextEntry::make('accreditation_end_date')
                            ->label(__('app.accreditation_end_date'))
                            ->dateTime(),

                        TextEntry::make('request_notes')
                            ->label(__('app.request_notes'))
                            ->placeholder(__('app.no_value'))
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label(__('app.created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('app.updated_at'))
                            ->dateTime(),
                    ]),

                Section::make(__('app.admin_review'))
                    ->description(__('app.admin_review_description'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('app.status'))
                            ->badge(),

                        TextEntry::make('reviewed_at')
                            ->label(__('app.reviewed_at'))
                            ->dateTime()
                            ->placeholder(__('app.no_value')),

                        TextEntry::make('admin_notes')
                            ->label(__('app.admin_notes'))
                            ->placeholder(__('app.no_value'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
