<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas;

use App\Models\CertifiedCenterPaymentAgentPerson;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CertifiedCenterFinancialRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('certified_center_id')
                    ->label(__('app.certified_center'))
                    ->relationship('certifiedCenter', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('agent_person_id')
                    ->label(__('app.agent_person'))
                    ->options(function (callable $get) {
                        $centerId = $get('certified_center_id');
                        if (!$centerId) {
                            return [];
                        }
                        return CertifiedCenterPaymentAgentPerson::where('certified_center_id', $centerId)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),
                TextInput::make('total_payment')
                    ->label(__('app.total_amount'))
                    ->numeric()
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('amount_paid')
                    ->label(__('app.paid_amount'))
                    ->numeric()
                    ->required()
                    ->live(onBlur: true),
                Textarea::make('reason')
                    ->label(__('app.notes'))
                    ->columnSpanFull(),
                DatePicker::make('date')
                    ->label(__('app.date'))
                    ->required()
                    ->default(now()),
            ]);
    }
}
