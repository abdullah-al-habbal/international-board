<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainerDocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_published')
                    ->label(__('app.is_published'))
                    ->required(),
            ]);
    }
}
