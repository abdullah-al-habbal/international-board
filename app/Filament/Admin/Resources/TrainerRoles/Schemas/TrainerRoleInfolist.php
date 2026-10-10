<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TrainerRoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name_en')
                    ->label(__('app.name_english'))
                    ->state(fn ($record) => $record->getTranslation('name', 'en') ?? __('app.no_english')),

                TextEntry::make('name_ar')
                    ->label(__('app.name_arabic'))
                    ->state(fn ($record) => $record->getTranslation('name', 'ar') ?? __('app.no_arabic')),

                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
