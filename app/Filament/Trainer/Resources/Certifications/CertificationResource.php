<?php

namespace App\Filament\Trainer\Resources\Certifications;

use App\Filament\Trainer\Resources\Certifications\Pages\CreateCertification;
use App\Filament\Trainer\Resources\Certifications\Pages\EditCertification;
use App\Filament\Trainer\Resources\Certifications\Pages\ListCertifications;
use App\Filament\Trainer\Resources\Certifications\Pages\ViewCertification;
use App\Filament\Trainer\Resources\Certifications\Schemas\CertificationForm;
use App\Filament\Trainer\Resources\Certifications\Schemas\CertificationInfolist;
use App\Filament\Trainer\Resources\Certifications\Tables\CertificationsTable;
use App\Models\Certification;
use App\Models\Trainer;
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
        return true;
    }

    /**
     * Certifications an admin issued and assigned to this trainer are listed
     * and viewable, but not editable or deletable — the trainer did not issue
     * them and must not be able to alter the record.
     */
    public static function canEdit(Model $record): bool
    {
        return static::wasAuthoredByCurrentTrainer($record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::wasAuthoredByCurrentTrainer($record);
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

    private static function wasAuthoredByCurrentTrainer(Model $record): bool
    {
        $trainer = Auth::guard('trainer')->user();

        return $trainer instanceof Trainer
            && $record->creator_type === Trainer::class
            && (int) $record->creator_id === $trainer->id;
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

        $trainer = Auth::guard('trainer')->user();

        // Includes certifications an admin issued and assigned to this trainer,
        // so the panel agrees with their public profile. Assigned records are
        // read-only — see canEdit()/canDelete().
        if ($trainer instanceof Trainer) {
            $query->forTrainer($trainer->id);
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
