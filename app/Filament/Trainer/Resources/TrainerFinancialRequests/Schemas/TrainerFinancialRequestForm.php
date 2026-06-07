<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Schemas;

use App\Models\CertifiedCenterPaymentAgentPerson;
use Filament\Forms\Components\DatePicker;
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
                    ->options(function () {
                        return CertifiedCenterPaymentAgentPerson::with('certifiedCenter')
                            ->get()
                            ->groupBy(fn ($ap) => $ap->certifiedCenter?->name ?? __('app.unassigned'))
                            ->map(fn ($group) => $group->pluck('name', 'id'));
                    })
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
