<?php
// app/Services/Web/SeoService.php
declare(strict_types=1);

namespace App\Services\Web;

use Str;

final class SeoService
{
    private string $title;
    private string $description;
    private string $image;

    public function __construct()
    {
        $this->title = __('web.default_title');
        $this->description = __('web.pages.home.hero_text');
        $this->image = asset('assets/website/images/logo.png');
    }

    public function setTitle(?string $title): self
    {
        if ($title) {
            $this->title = $title . ' | ' . __('web.default_title');
        }
        return $this;
    }

    public function setDescription(?string $description): self
    {
        if ($description) {
            $this->description = Str::limit(strip_tags($description), 160);
        }
        return $this;
    }

    public function setImage(?string $image): self
    {
        if ($image) {
            $this->image = $image;
        }
        return $this;
    }

    public function render(): string
    {
        return sprintf(
            '<meta name="description" content="%s">' . "\n" .
            '<meta property="og:title" content="%s">' . "\n" .
            '<meta property="og:description" content="%s">' . "\n" .
            '<meta property="og:image" content="%s">' . "\n" .
            '<meta name="twitter:card" content="summary_large_image">',
            htmlspecialchars($this->description),
            htmlspecialchars($this->title),
            htmlspecialchars($this->description),
            htmlspecialchars($this->image)
        );
    }
}
