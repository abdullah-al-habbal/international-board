<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TrainerAccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('requested_start_date')
                    ->label(__('app.requested_start_date'))
                    ->required()
                    ->default(now()),
                DatePicker::make('requested_end_date')
                    ->label(__('app.requested_end_date'))
                    ->required()
                    ->default(now()->addYear()),
                Textarea::make('request_notes')
                    ->label(__('app.notes'))
                    ->placeholder(__('app.request_notes_placeholder'))
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->disabled()
                    ->visible(fn($record) => $record !== null && !empty($record->admin_notes))
                    ->columnSpanFull(),
            ]);
    }
}
