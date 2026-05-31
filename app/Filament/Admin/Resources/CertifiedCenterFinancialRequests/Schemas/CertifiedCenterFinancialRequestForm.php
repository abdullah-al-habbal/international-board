<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas;

use App\Models\CertifiedCenterPaymentAgentPerson;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

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
                        if (! $centerId) {
                            return [];
                        }

                        return CertifiedCenterPaymentAgentPerson::where('certified_center_id', $centerId)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->rule(function (callable $get) {
                        $centerId = $get('certified_center_id');

                        return function (string $attribute, mixed $value, \Closure $fail) use ($centerId) {
                            if (! $centerId) {
                                return;
                            }
                            $exists = CertifiedCenterPaymentAgentPerson::where('id', $value)
                                ->where('certified_center_id', $centerId)
                                ->exists();
                            if (! $exists) {
                                $fail(__('The selected agent person does not belong to the selected center.'));
                            }
                        };
                    }),
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
