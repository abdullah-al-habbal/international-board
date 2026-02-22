<?php

namespace App\Filament\Admin\Resources\Certifications;

use App\Filament\Admin\Resources\Certifications\Pages\CreateCertification;
use App\Filament\Admin\Resources\Certifications\Pages\EditCertification;
use App\Filament\Admin\Resources\Certifications\Pages\ListCertifications;
use App\Filament\Admin\Resources\Certifications\Pages\ViewCertification;
use App\Filament\Admin\Resources\Certifications\Schemas\CertificationForm;
use App\Filament\Admin\Resources\Certifications\Schemas\CertificationInfolist;
use App\Filament\Admin\Resources\Certifications\Tables\CertificationsTable;
use App\Models\Certification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertificationResource extends Resource
{
    protected static ?string $model = Certification::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'accredited_serial_number';

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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with([
            'trainee',
            'country',
            'trainer',
            'certifiedCenter',
            'documentType',
        ]);
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
