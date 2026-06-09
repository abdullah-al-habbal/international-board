<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TrainerAccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('request_notes')
                ->label(__('app.notes'))
                ->placeholder(__('app.request_notes_placeholder'))
                ->columnSpanFull(),
        ]);
    }
}
