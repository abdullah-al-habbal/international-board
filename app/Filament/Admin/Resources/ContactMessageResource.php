<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactMessage\Pages\ListContactMessages;
use App\Filament\Admin\Resources\ContactMessage\Pages\ViewContactMessage;
use App\Filament\Admin\Resources\ContactMessage\Schemas\ContactMessageInfolist;
use App\Filament\Admin\Resources\ContactMessage\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedInbox;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.communication');
    }

    protected static ?int $navigationSort = 2; // adjust as needed

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.contact_messages');
    }

    public static function getModelLabel(): string
    {
        return __('app.contact_message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.contact_messages');
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
