<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([
            TextInput::make('name')
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('app.email'))
                ->email()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable(),

            TextInput::make('phone')
                ->label(__('app.phone'))
                ->tel()
                ->maxLength(255)
                ->nullable(),

            Select::make('country_id')
                ->label(__('app.country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->nullable(),

            FileUpload::make('avatar')
                ->label(__('app.avatar'))
                ->image()
                ->avatar()
                ->directory('trainers/avatars')
                ->visibility('public')
                ->nullable(),

            Select::make('specializations')
                ->label(__('app.specializations'))
                ->multiple()
                ->options([
                    'Training' => __('app.specialization_training'),
                    'Consulting' => __('app.specialization_consulting'),
                    'Leadership' => __('app.specialization_leadership'),
                    'Management' => __('app.specialization_management'),
                    'Communication' => __('app.specialization_communication'),
                    'Technical Skills' => __('app.specialization_technical_skills'),
                    'Soft Skills' => __('app.specialization_soft_skills'),
                    'Project Management' => __('app.specialization_project_management'),
                    'Digital Marketing' => __('app.specialization_digital_marketing'),
                    'HR' => __('app.specialization_hr'),
                    'Quality Management' => __('app.specialization_quality_management'),
                    'Entrepreneurship' => __('app.specialization_entrepreneurship'),
                ])
                ->searchable()
                ->nullable()
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label(__('app.active'))
                ->default(true)
                ->inline(false),
        ]);
    }
}
