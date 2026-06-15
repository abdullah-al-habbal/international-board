<?php
// app\Filament\Traits\RedirectsToShowPage.php
declare(strict_types=1);

namespace App\Filament\Traits;

trait RedirectsToShowPage
{
    protected function getRedirectUrl(): string
    {
        $resource = $this->getResource();
        $record = $this->record ?? $this->getRecord();

        if ($record && $resource::hasPage('view')) {
            return $resource::getUrl('view', ['record' => $record]);
        }

        return $resource::getUrl('index');
    }
}
