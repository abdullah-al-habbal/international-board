<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('certifiedCenter.name')
                    ->label(__('app.certified_center'))
                    ->placeholder(__('app.not_assigned'))
                    ->default(__('app.not_assigned'))
                    ->badge()
                    ->color(fn ($record) => $record->certified_center_id ? 'primary' : 'gray'),

                TextEntry::make('documentType.name')
                    ->label(__('app.document_type'))
                    ->getStateUsing(function ($record) {
                        if (! $record->documentType) {
                            return __('app.no_document_type');
                        }

                        $name = $record->documentType->name;
                        if (empty($name)) {
                            $name = $record->documentType->getTranslation('name', app()->getLocale());
                        }

                        return $name ?: $record->documentType->key;
                    })
                    ->badge()
                    ->color(fn ($record) => $record->document_type_id ? 'info' : 'gray'),

                TextEntry::make('trainee.name')
                    ->label(__('app.trainee_name'))
                    ->placeholder(__('app.not_assigned'))
                    ->default(__('app.not_assigned')),

                TextEntry::make('accredited_serial_number')
                    ->label(__('app.accredited_serial_number'))
                    ->copyable()
                    ->copyMessage(__('app.copied'))
                    ->copyMessageDuration(1500),

                TextEntry::make('document_code')
                    ->label(__('app.document_code'))
                    ->placeholder('-'),

                TextEntry::make('accreditation_date')
                    ->label(__('app.accreditation_date'))
                    ->placeholder('-')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '—';
                        }
                        try {
                            return Carbon::parse($state)->format('M d, Y');
                        } catch (\Exception $e) {
                            return __('app.invalid_date');
                        }
                    }),

                TextEntry::make('trainer.name')
                    ->label(__('app.trainer'))
                    ->placeholder(__('app.not_assigned'))
                    ->default(__('app.not_assigned'))
                    ->badge()
                    ->color(fn ($record) => $record->trainer_id ? 'warning' : 'gray'),

                TextEntry::make('country.name')
                    ->label(__('app.country'))
                    ->placeholder(__('app.not_assigned'))
                    ->default(__('app.not_assigned'))
                    ->badge()
                    ->color(fn ($record) => $record->country_id ? 'info' : 'gray'),

                TextEntry::make('paper_received')
                    ->label(__('app.paper_received'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('app.yes') : __('app.no'))
                    ->color(fn ($state) => $state ? 'success' : 'danger'),

                TextEntry::make('notes')
                    ->label(__('app.notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
