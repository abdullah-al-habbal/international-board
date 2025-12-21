<?php

namespace App\Filament\Center\Resources\CenterTypeRequests\Schemas;

use App\Enums\CenterTypeRequestStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CenterTypeRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->label(__('app.request_type'))
                    ->badge(),
                TextEntry::make('documentType.name')
                    ->label(__('app.document_type'))
                    ->placeholder('—'),
                TextEntry::make('requested_name')
                    ->label(__('app.requested_name')),
                TextEntry::make('requested_description')
                    ->label(__('app.requested_description'))
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label(__('app.status'))
                    ->badge(),
                TextEntry::make('rejection_message')
                    ->label(__('app.rejection_message'))
                    ->columnSpanFull()
                    ->visible(fn($record) => $record && $record->status === CenterTypeRequestStatus::Rejected),
                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime(),
            ]);
    }
}
