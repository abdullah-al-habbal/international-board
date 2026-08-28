<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas;

use App\Filament\FinancialRequests\FinancialRequestFields;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertifiedCenterFinancialRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('id')
                ->label(__('app.id'))
                ->size('sm')
                ->columnSpan(1),
            TextEntry::make('requestable.name')
                ->label(__('app.certified_center'))
                ->weight('bold')
                ->columnSpanFull(),
            ...FinancialRequestFields::amountEntries(),
            TextEntry::make('date')
                ->label(__('app.date'))
                ->date()
                ->columnSpan(1),
            TextEntry::make('reason')
                ->label(__('app.notes'))
                ->columnSpanFull(),
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
