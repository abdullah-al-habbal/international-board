<?php

// filePath: app/Filament/Center/Resources/AccreditationRequests/AccreditationRequestResource.php
declare(strict_types=1);

namespace App\Filament\Center\Resources\AccreditationRequests;

use App\Filament\Center\Resources\AccreditationRequests\Pages\CreateAccreditationRequest;
use App\Filament\Center\Resources\AccreditationRequests\Pages\ListAccreditationRequests;
use App\Filament\Center\Resources\AccreditationRequests\Pages\ViewAccreditationRequest;
use App\Filament\Center\Resources\AccreditationRequests\Schemas\AccreditationRequestForm;
use App\Filament\Center\Resources\AccreditationRequests\Schemas\AccreditationRequestInfolist;
use App\Filament\Center\Resources\AccreditationRequests\Tables\AccreditationRequestsTable;
use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AccreditationRequestResource extends Resource
{
    protected static ?string $model = AccreditationRequest::class;

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'id';

    // ──────────────────────────────────────────────────────────────────────────
    // Navigation
    // ──────────────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedRectangleStack;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.accreditation_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.accreditation_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.accreditation_requests');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getEloquentQuery()->count() > 0 ? 'warning' : 'primary';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Authorization
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * A center may only create a new request when it has no currently active
     * accreditation (i.e. no Approved request covering today).
     * This acts as the "subscription" gate described in the requirements.
     */
    public static function canCreate(): bool
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if (! $center instanceof CertifiedCenter) {
            return false;
        }

        return ! $center->hasActiveAccreditationRequest();
    }

    /** Centers may not edit submitted requests — admin-only operation. */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /** Centers may not delete requests. */
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    /** Centers can always view their own requests. */
    public static function canViewAny(): bool
    {
        return true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Query scope — a center only sees its own requests
    // ──────────────────────────────────────────────────────────────────────────

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('certified_center_id', auth('certified_center')->id());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Schemas / Table
    // ──────────────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return AccreditationRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccreditationRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccreditationRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccreditationRequests::route('/'),
            'create' => CreateAccreditationRequest::route('/create'),
            'view' => ViewAccreditationRequest::route('/{record}'),
            // 'edit' intentionally omitted — canEdit() returns false
        ];
    }
}
