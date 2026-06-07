<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AccreditationRequests\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('certified_center_id')
                    ->label(__('app.certified_center'))
                    ->relationship('certifiedCenter', 'name')
                    ->disabled()
                    ->dehydrated(false)
                    ->required(),

                DateTimePicker::make('requested_start_date')
                    ->label(__('app.requested_start_date'))
                    ->disabled()
                    ->dehydrated(false)
                    ->required(),

                DateTimePicker::make('requested_end_date')
                    ->label(__('app.requested_end_date'))
                    ->disabled()
                    ->dehydrated(false)
                    ->required(),

                Textarea::make('request_notes')
                    ->label(__('app.request_notes'))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->columnSpanFull(),

                TextInput::make('reviewed_by')
                    ->label(__('app.reviewed_by'))
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => $state ? User::find($state)?->name : '-'),

                DateTimePicker::make('reviewed_at')
                    ->label(__('app.reviewed_at'))
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
