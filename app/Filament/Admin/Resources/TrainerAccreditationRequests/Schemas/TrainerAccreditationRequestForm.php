<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAccreditationRequests\Schemas;

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
                Textarea::make('request_notes')
                    ->label(__('app.notes'))
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->columnSpanFull(),
            ]);
    }
}
