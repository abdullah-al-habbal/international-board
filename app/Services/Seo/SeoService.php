<?php

declare(strict_types=1);

namespace App\Services\Seo;

use Illuminate\Support\Facades\View;

final class SeoService
{
    public function setMeta(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null,
        ?string $url = null
    ): void {
        $defaultTitle = config('app.name', __('web.default_title'));
        $title = $title ? "{$title} | {$defaultTitle}" : $defaultTitle;

        $meta = [
            'title' => $title,
            'description' => $description ?? __('web.pages.home.hero_text'),
            'image' => $image ?? asset('assets/website/images/logo.webp'),
            'url' => $url ?? url()->current(),
        ];

        View::share('seo', $meta);
    }
}
