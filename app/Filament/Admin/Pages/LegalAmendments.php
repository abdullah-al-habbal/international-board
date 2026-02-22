<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LegalAmendments extends Page
{
    protected static string|BackedEnum|null $navigationIcon = null;

    protected string $view = 'filament.admin.pages.legal-amendments';

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('app.legal_amendments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.settings');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedDocumentText;
    }
}
