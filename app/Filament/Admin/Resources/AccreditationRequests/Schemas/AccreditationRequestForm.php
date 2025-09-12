<?php

namespace App\Filament\Admin\Resources\AccreditationRequests\Schemas;

use App\Enums\AccreditationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('certified_center_id')
                    ->relationship('certifiedCenter', 'name')
                    ->required(),
                DateTimePicker::make('requested_start_date')
                    ->required(),
                DateTimePicker::make('requested_end_date')
                    ->required(),
                Textarea::make('request_notes')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(AccreditationStatus::class)
                    ->default('pending')
                    ->required(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                TextInput::make('reviewed_by')
                    ->numeric(),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
