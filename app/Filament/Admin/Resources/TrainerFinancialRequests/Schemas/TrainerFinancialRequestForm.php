<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Schemas;

use App\Models\CertifiedCenterPaymentAgentPerson;
use App\Models\Trainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class TrainerFinancialRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trainer_id')
                    ->label(__('app.trainer'))
                    ->relationship('trainer', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
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
                    ->live()
                    ->required(),
                TextInput::make('total_payment')
                    ->label(__('app.total_amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->live(onBlur: true),
                TextInput::make('amount_paid')
                    ->label(__('app.paid_amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->rule(function (callable $get) {
                        return Rule::lte((string) ($get('total_payment') ?? 0));
                    })
                    ->live(onBlur: true),
                Textarea::make('reason')
                    ->label(__('app.notes'))
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                DatePicker::make('date')
                    ->label(__('app.date'))
                    ->required()
                    ->default(now())
                    ->beforeOrEqual('today'),
            ]);
    }
}
