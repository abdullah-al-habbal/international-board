<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Schemas;

use App\Models\CertifiedCenterDocumentType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertifiedCenterDocumentTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('id')
                ->label(__('app.id'))
                ->size('sm')
                ->columnSpan(1),

            TextEntry::make('certifiedCenter.name')
                ->label(__('app.certified_center'))
                ->weight('bold')
                ->columnSpanFull(),

            TextEntry::make('documentType.key')
                ->label(__('app.document_type_key'))
                ->badge()
                ->color('primary')
                ->columnSpan(1),

            TextEntry::make('documentType.name')
                ->label(__('app.document_type_name'))
                ->columnSpan(1),

            TextEntry::make('certifications_count')
                ->label(__('app.usage_count'))
                ->badge()
                ->color('success')
                ->state(fn (CertifiedCenterDocumentType $record): int => $record->certifications()->where('certified_center_id', $record->certified_center_id)->count())
                ->columnSpan(1),

            TextEntry::make('created_at')
                ->label(__('app.assigned_at'))
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
