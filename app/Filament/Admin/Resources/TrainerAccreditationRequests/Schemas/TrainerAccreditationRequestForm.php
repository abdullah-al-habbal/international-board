<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAccreditationRequests\Schemas;

use App\Models\Trainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TrainerAccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trainer_id')
                    ->label(__('app.trainer'))
                    ->relationship('trainer', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
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
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->columnSpanFull(),
            ]);
    }
}
