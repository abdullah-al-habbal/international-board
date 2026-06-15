<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BlogPosts\Pages;

use App\Filament\Admin\Resources\BlogPosts\BlogPostResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = BlogPostResource::class;
}