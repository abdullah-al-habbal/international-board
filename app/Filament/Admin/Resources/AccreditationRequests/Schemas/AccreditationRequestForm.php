<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AccreditationRequests\Schemas;

use App\Enums\AccreditationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('certified_center_id')
                    ->label(__('app.certified_center'))
                    ->relationship('certifiedCenter', 'name')
                    ->required(),
                DateTimePicker::make('requested_start_date')
                    ->label(__('app.requested_start_date'))
                    ->required(),
                DateTimePicker::make('requested_end_date')
                    ->label(__('app.requested_end_date'))
                    ->required(),
                Textarea::make('request_notes')
                    ->label(__('app.request_notes'))
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('app.status'))
                    ->options(AccreditationStatus::class)
                    ->default(AccreditationStatus::Pending->value)
                    ->required(),
                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->columnSpanFull(),
                Select::make('reviewed_by')
                    ->label(__('app.reviewed_by'))
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->placeholder(__('app.no_value')),
                DateTimePicker::make('reviewed_at')
                    ->label(__('app.reviewed_at')),
            ]);
    }
}
