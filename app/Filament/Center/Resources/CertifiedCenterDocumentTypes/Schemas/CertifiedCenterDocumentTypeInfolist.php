<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertifiedCenterDocumentTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('key')
                ->label(__('app.document_type_key'))
                ->badge()
                ->color('primary')
                ->columnSpan(1),

            TextEntry::make('name.en')
                ->label(__('app.name_english'))
                ->columnSpan(1),

            TextEntry::make('name.ar')
                ->label(__('app.name_arabic'))
                ->columnSpan(1),

            TextEntry::make('status')
                ->label(__('app.status'))
                ->badge()
                ->color(fn ($state) => $state?->color() ?? 'gray')
                ->formatStateUsing(fn ($state) => $state?->label() ?? '—')
                ->columnSpan(1),

            TextEntry::make('reviewer.name')
                ->label(__('app.reviewed_by'))
                ->placeholder(__('app.no_value'))
                ->columnSpan(1),

            TextEntry::make('created_at')
                ->label(__('app.created_at'))
                ->dateTime()
                ->icon('heroicon-o-calendar')
                ->columnSpan(1),

            TextEntry::make('updated_at')
                ->label(__('app.updated_at'))
                ->dateTime()
                ->since()
                ->icon('heroicon-o-clock')
                ->columnSpan(1),
        ])->columns(2);
    }
}
