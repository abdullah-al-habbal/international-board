<?php

namespace App\Filament\Admin\Resources\EditRequests\Schemas;

use App\Enums\EditRequestStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EditRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label(__('app.status'))
                    ->options(EditRequestStatus::class)
                    ->required(),
                Textarea::make('rejection_reason')
                    ->label(__('app.rejection_reason'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
