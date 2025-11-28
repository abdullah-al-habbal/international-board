<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Memberships\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MembershipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            ImageEntry::make('descriptive_image')
                ->label(__('Image'))
                ->defaultImageUrl(url('/images/default-membership.png'))
                ->columnSpanFull(),

            TextEntry::make('title')
                ->label(__('Title'))
                ->weight('bold')
                ->size('lg')
                ->columnSpanFull(),

            TextEntry::make('description')
                ->label(__('Description'))
                ->markdown()
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('sort_order')
                ->label(__('Sort Order'))
                ->numeric()
                ->icon('heroicon-o-bars-3')
                ->columnSpan(1),

            IconEntry::make('is_active')
                ->label(__('Status'))
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger')
                ->columnSpan(1),

            TextEntry::make('created_at')
                ->label(__('Created At'))
                ->dateTime()
                ->icon('heroicon-o-calendar')
                ->columnSpan(1),

            TextEntry::make('updated_at')
                ->label(__('Updated At'))
                ->dateTime()
                ->since()
                ->icon('heroicon-o-clock')
                ->columnSpan(1),
        ])->columns(2);
    }
}
