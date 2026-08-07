<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas;

use App\Models\AgentPerson;
use App\Models\CertifiedCenter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertifiedCenterFinancialRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('requestable_type')
                    ->default(CertifiedCenter::class),
                Select::make('requestable_id')
                    ->label(__('app.certified_center'))
                    ->options(CertifiedCenter::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('agent_person_id')
                    ->label(__('app.agent_person'))
                    ->options(AgentPerson::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
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
                    ->maxValue(fn (callable $get): float => (float) ($get('total_payment') ?? 0))
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
