<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Schemas;

use App\Filament\Components\DatePicker;
use App\Models\AgentPerson;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainerFinancialRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('agent_person_id')
                    ->label(__('app.agent_person'))
                    ->options(AgentPerson::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('total_payment')
                    ->label(__('app.total_amount'))
                    ->numeric()
                    ->required(),
                TextInput::make('amount_paid')
                    ->label(__('app.paid_amount'))
                    ->numeric()
                    ->required(),
                DatePicker::make('date')
                    ->label(__('app.date'))
                    ->required(),
                Textarea::make('reason')
                    ->label(__('app.notes'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
