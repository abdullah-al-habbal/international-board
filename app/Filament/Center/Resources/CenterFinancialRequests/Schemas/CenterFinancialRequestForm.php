<?php

// filePath: app/Filament/Center/Resources/CenterFinancialRequests/Schemas/CenterFinancialRequestForm.php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterFinancialRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CenterFinancialRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('agent_person_id')
                    ->label(__('app.agent_person'))
                    ->formatStateUsing(fn ($record) => $record?->agentPerson?->name)
                    ->disabled(),
                TextInput::make('total_payment')
                    ->money('USD')
                    ->disabled(),
                TextInput::make('amount_paid')
                    ->money('USD')
                    ->disabled(),
                TextInput::make('remaining_amount')
                    ->money('USD')
                    ->disabled(),
                DatePicker::make('date')->disabled(),
                Textarea::make('reason')->disabled()->columnSpanFull(),
            ]);
    }
}
