<?php

namespace App\Filament\Admin\Resources\ContactMessage\Pages;

use App\Filament\Admin\Resources\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('app.name')),
                TextEntry::make('email')
                    ->label(__('app.email')),
                TextEntry::make('message')
                    ->label(__('app.message')),
                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime(),
                TextEntry::make('is_read')
                    ->label(__('app.is_read'))
                    ->formatStateUsing(fn ($state) => $state ? __('app.yes') : __('app.no')),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->update(['is_read' => true]);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
