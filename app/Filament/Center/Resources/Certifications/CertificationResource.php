<?php

namespace App\Filament\Center\Resources\Certifications;

use App\Filament\Center\Resources\Certifications\Pages\CreateCertification;
use App\Filament\Center\Resources\Certifications\Pages\EditCertification;
use App\Filament\Center\Resources\Certifications\Pages\ListCertifications;
use App\Filament\Center\Resources\Certifications\Pages\ViewCertification;
use App\Filament\Center\Resources\Certifications\Schemas\CertificationForm;
use App\Filament\Center\Resources\Certifications\Schemas\CertificationInfolist;
use App\Filament\Center\Resources\Certifications\Tables\CertificationsTable;
use App\Models\Certification;
use App\Models\CertifiedCenter;
use App\Services\Accreditation\AccreditationGateService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CertificationResource extends Resource
{
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

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?string $model = Certification::class;

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'accredited_serial_number';

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
        return __('app.certifications');
    }

    public static function getModelLabel(): string
    {
        return __('app.certification');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.certifications');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $center = Auth::guard('certified_center')->user();

        if ($center instanceof CertifiedCenter) {
            $query->where('creator_type', CertifiedCenter::class)
                  ->where('creator_id', $center->id);
        }

        return $query->with([
            'trainee',
            'country',
            'creator',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return CertificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CertificationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertifications::route('/'),
            'create' => CreateCertification::route('/create'),
            'view' => ViewCertification::route('/{record}'),
            'edit' => EditCertification::route('/{record}/edit'),
        ];
    }
}
