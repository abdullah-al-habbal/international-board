<?php

namespace App\Filament\Components;

use Filament\Forms\Components\DatePicker as FilamentDatePicker;

class DatePicker extends FilamentDatePicker
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->native(false)
            ->format('Y-m-d')
            ->displayFormat('d/m/Y')
            ->locale(app()->getLocale());
    }
}
