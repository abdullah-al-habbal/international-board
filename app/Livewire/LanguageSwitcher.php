<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function switchLanguage(string $locale): void
    {
        if (!in_array($locale, ['en', 'ar'], true)) {
            return;
        }

        Session::put('locale', $locale);
        app()->setLocale($locale);

        $this->redirect(request()->url());
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }

    public function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    public function getAvailableLocales(): array
    {
        return [
            'en' => [
                'name' => 'English',
                'flag' => '🇺🇸',
                'native' => 'English'
            ],
            'ar' => [
                'name' => 'Arabic',
                'flag' => '🇸🇦',
                'native' => 'العربية'
            ]
        ];
    }
}
