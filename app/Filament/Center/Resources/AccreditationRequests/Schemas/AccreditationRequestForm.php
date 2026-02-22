<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\AccreditationRequests\Schemas;

use App\Models\CertifiedCenter;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        return $schema
            ->columns(2)
            ->components([
                Placeholder::make('center_name')
                    ->label(__('app.certified_center'))
                    ->content($center?->name ?? '-')
                    ->columnSpanFull(),

                DateTimePicker::make('requested_start_date')
                    ->label(__('app.requested_start_date'))
                    ->required()
                    ->minDate(now()),

                DateTimePicker::make('requested_end_date')
                    ->label(__('app.requested_end_date'))
                    ->required()
                    ->after('requested_start_date'),

                Textarea::make('request_notes')
                    ->label(__('app.request_notes'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
