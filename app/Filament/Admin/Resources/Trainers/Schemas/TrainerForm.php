<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),

            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable()
                ->columnSpan(1),

            TextInput::make('phone')
                ->label(__('Phone'))
                ->tel()
                ->maxLength(255)
                ->nullable()
                ->columnSpan(1),

            Select::make('country_id')
                ->label(__('Country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->createOptionForm([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->required()->maxLength(10),
                    TextInput::make('code_2')->required()->maxLength(10),
                ])
                ->columnSpan(1),

            FileUpload::make('avatar')
                ->label(__('Avatar'))
                ->image()
                ->avatar()
                ->directory('trainers/avatars')
                ->visibility('public')
                ->nullable()
                ->columnSpan(1),

            Textarea::make('address')
                ->label(__('Address'))
                ->maxLength(65535)
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),

            Textarea::make('bio')
                ->label(__('Biography'))
                ->maxLength(65535)
                ->rows(4)
                ->nullable()
                ->columnSpanFull(),

            Select::make('specializations')
                ->label(__('Specializations'))
                ->multiple()
                ->options([
                    'Training' => __('Training'),
                    'Consulting' => __('Consulting'),
                    'Leadership' => __('Leadership'),
                    'Management' => __('Management'),
                    'Communication' => __('Communication'),
                    'Technical Skills' => __('Technical Skills'),
                    'Soft Skills' => __('Soft Skills'),
                    'Project Management' => __('Project Management'),
                    'Digital Marketing' => __('Digital Marketing'),
                    'HR' => __('Human Resources'),
                    'Quality Management' => __('Quality Management'),
                    'Entrepreneurship' => __('Entrepreneurship'),
                ])
                ->searchable()
                ->nullable()
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label(__('Active'))
                ->default(true)
                ->inline(false)
                ->columnSpanFull(),
        ])->columns(2);
    }
}
