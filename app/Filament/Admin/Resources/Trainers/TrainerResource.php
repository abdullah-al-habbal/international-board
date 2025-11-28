<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers;

use App\Filament\Admin\Resources\Trainers\Pages\CreateTrainer;
use App\Filament\Admin\Resources\Trainers\Pages\EditTrainer;
use App\Filament\Admin\Resources\Trainers\Pages\ListTrainers;
use App\Filament\Admin\Resources\Trainers\Pages\ViewTrainer;
use App\Filament\Admin\Resources\Trainers\Schemas\TrainerForm;
use App\Filament\Admin\Resources\Trainers\Schemas\TrainerInfolist;
use App\Filament\Admin\Resources\Trainers\Tables\TrainersTable;
use App\Models\Trainer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrainerResource extends Resource
{
    protected static ?string $model = Trainer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('app.trainers');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainers');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainers::route('/'),
            'create' => CreateTrainer::route('/create'),
            'view' => ViewTrainer::route('/{record}'),
            'edit' => EditTrainer::route('/{record}/edit'),
        ];
    }
}
