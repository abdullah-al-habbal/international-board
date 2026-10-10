<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterFinancialRequests\Schemas;

use App\Filament\FinancialRequests\FinancialRequestFields;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CenterFinancialRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make(__('app.financial_details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('agentPerson.name')
                            ->label(__('app.agent_person')),
                        ...FinancialRequestFields::amountEntries(),
                        TextEntry::make('date')
                            ->label(__('app.date'))
                            ->date(),
                        TextEntry::make('reason')
                            ->label(__('app.notes'))
                            ->placeholder(__('app.no_value'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
