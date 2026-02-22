<?php

namespace App\Filament\Admin\Resources\AccreditationRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccreditationRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.request_details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('certifiedCenter.name')
                            ->label(__('app.certified_center'))
                            ->columnSpanFull(),

                        TextEntry::make('requested_start_date')
                            ->label(__('app.requested_start_date'))
                            ->dateTime(),

                        TextEntry::make('requested_end_date')
                            ->label(__('app.requested_end_date'))
                            ->dateTime(),

                        TextEntry::make('request_notes')
                            ->label(__('app.request_notes'))
                            ->placeholder(__('app.no_value'))
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label(__('app.created_at'))
                            ->dateTime(),
                    ]),

                Section::make(__('app.admin_review'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('app.status'))
                            ->badge(),

                        TextEntry::make('reviewer.name')
                            ->label(__('app.reviewed_by'))
                            ->placeholder(__('app.no_value')),

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
