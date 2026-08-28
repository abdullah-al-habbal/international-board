<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Schemas;

use App\Filament\Components\DatePicker;
use App\Filament\FinancialRequests\FinancialRequestFields;
use App\Models\Trainer;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TrainerFinancialRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('requestable_type')
                    ->default(Trainer::class),
                FinancialRequestFields::requestableSelect(Trainer::class, __('app.trainer')),
                FinancialRequestFields::agentPersonSelect(),
                ...FinancialRequestFields::amountFields(),
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
