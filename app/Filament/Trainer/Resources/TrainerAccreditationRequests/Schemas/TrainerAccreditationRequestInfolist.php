<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Schemas;

use App\Enums\AccreditationStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainerAccreditationRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make(__('app.request_details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('app.requested_at'))
                            ->dateTime(),

                        TextEntry::make('request_notes')
                            ->label(__('app.notes'))
                            ->placeholder(__('app.no_value'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('app.admin_review'))
                    ->description(__('app.admin_review_description'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('app.status'))
                            ->badge()
                            ->color(fn (AccreditationStatus $state): string => $state->color()),

                        TextEntry::make('reviewer.name')
                            ->label(__('app.reviewed_by'))
                            ->placeholder(__('app.no_value')),

                        TextEntry::make('accreditation_start_date')
                            ->label(__('app.start_date'))
                            ->date()
                            ->placeholder(__('app.no_value')),

                        TextEntry::make('accreditation_end_date')
                            ->label(__('app.end_date'))
                            ->date()
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
