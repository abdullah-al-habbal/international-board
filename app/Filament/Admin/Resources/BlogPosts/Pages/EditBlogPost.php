<?php

namespace App\Filament\Admin\Resources\BlogPosts\Pages;

use App\Filament\Admin\Resources\BlogPosts\BlogPostResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
