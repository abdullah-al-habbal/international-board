<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Schemas;

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('creator')
                    ->label(__('app.issued_by'))
                    ->getStateUsing(function ($record) {
                        if (!$record->creator) {
                            return __('app.not_assigned');
                        }
                        $label = match ($record->creator_type) {
                            User::class => __('app.board_admin'),
                            CertifiedCenter::class => __('app.certified_center'),
                            Trainer::class => __('app.trainer'),
                            default => __('app.unknown'),
                        };
                        return $label . ': ' . $record->creator->name;
                    })
                    ->badge()
                    ->color(fn ($record) => $record->creator_id ? 'primary' : 'gray'),

                TextEntry::make('trainee.name')
                    ->label(__('app.trainee_name'))
                    ->placeholder(__('app.not_assigned'))
                    ->default(__('app.not_assigned')),

                TextEntry::make('assignedTrainer.name')
                    ->label(__('app.assigned_trainer'))
                    ->placeholder(__('app.not_assigned'))
                    ->badge()
                    ->color('warning'),

                TextEntry::make('accredited_serial_number')
                    ->label(__('app.accredited_serial_number'))
                    ->copyable()
                    ->copyMessage(__('app.copied'))
                    ->copyMessageDuration(1500),

                TextEntry::make('document_code')
                    ->label(__('app.document_code')),

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
