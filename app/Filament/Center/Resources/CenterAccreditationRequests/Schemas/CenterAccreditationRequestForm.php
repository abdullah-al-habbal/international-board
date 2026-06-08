<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Schemas;

use App\Models\CertifiedCenter;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CenterAccreditationRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        return $schema
            ->columns(2)
            ->components([
                Placeholder::make('center_name')
                    ->label(__('app.certified_center'))
                    ->content($center?->name ?? '-')
                    ->columnSpanFull(),

                Textarea::make('request_notes')
                    ->label(__('app.request_notes'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
