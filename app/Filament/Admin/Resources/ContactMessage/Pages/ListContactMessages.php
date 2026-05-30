<?php

namespace App\Filament\Admin\Resources\ContactMessage\Pages;

use App\Filament\Admin\Resources\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;
}
