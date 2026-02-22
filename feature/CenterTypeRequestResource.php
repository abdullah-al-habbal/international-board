<?php

// filePath: app/Filament/Center/Resources/CenterTypeRequests/CenterTypeRequestResource.php
declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterTypeRequests;

use App\Filament\Center\Resources\CenterTypeRequests\Pages\CreateCenterTypeRequest;
use App\Filament\Center\Resources\CenterTypeRequests\Pages\ListCenterTypeRequests;
use App\Filament\Center\Resources\CenterTypeRequests\Pages\ViewCenterTypeRequest;
use App\Filament\Center\Resources\CenterTypeRequests\Schemas\CenterTypeRequestForm;
use App\Filament\Center\Resources\CenterTypeRequests\Schemas\CenterTypeRequestInfolist;
use App\Filament\Center\Resources\CenterTypeRequests\Tables\CenterTypeRequestsTable;
use App\Models\CenterTypeRequest;
use App\Services\Accreditation\AccreditationGateService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CenterTypeRequestResource extends Resource
{
    protected static ?string $model = CenterTypeRequest::class;

    // ──────────────────────────────────────────────────────────────────────────
    // Accreditation gate
    // ──────────────────────────────────────────────────────────────────────────

    public static function canCreate(): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canEdit(Model $record): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canDelete(Model $record): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canDeleteAny(): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Navigation
    // ──────────────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedBuildingOffice;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.center_type_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.center_type_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.center_type_requests');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Schemas / Table
    // ──────────────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return CenterTypeRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CenterTypeRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CenterTypeRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCenterTypeRequests::route('/'),
            'create' => CreateCenterTypeRequest::route('/create'),
            'view' => ViewCenterTypeRequest::route('/{record}'),
        ];
    }
}
