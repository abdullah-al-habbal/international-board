<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BlogPosts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BlogPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextEntry::make('title')
                    ->label(__('app.title'))
                    ->weight('bold')
                    ->size('xl')
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', app()->getLocale()) ?: '—'),

                TextEntry::make('slug')
                    ->label(__('app.slug'))
                    ->columnSpan(1),

                ImageEntry::make('image')
                    ->label(__('app.image'))
                    ->placeholder(__('app.no_image'))
                    ->columnSpan(1),

                IconEntry::make('is_published')
                    ->label(__('app.is_published'))
                    ->boolean()
                    ->columnSpan(1),

                TextEntry::make('published_at')
                    ->label(__('app.published_at'))
                    ->dateTime()
                    ->icon('heroicon-o-calendar')
                    ->columnSpan(1),

                TextEntry::make('excerpt')
                    ->label(__('app.excerpt'))
                    ->markdown()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($record) => $record->getTranslation('excerpt', app()->getLocale()) ?: '—'),

                TextEntry::make('content')
                    ->label(__('app.content'))
                    ->markdown()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($record) => $record->getTranslation('content', app()->getLocale()) ?: '—'),

                TextEntry::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->icon('heroicon-o-clock')
                    ->columnSpan(1),

                TextEntry::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->since()
                    ->icon('heroicon-o-clock')
                    ->columnSpan(1),

                TextEntry::make('id')
                    ->label(__('app.id'))
                    ->size('sm')
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
