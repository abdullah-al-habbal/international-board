<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LegalAmendments extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string $view = 'filament.admin.pages.legal-amendments';

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('app.legal_amendments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.settings');
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
