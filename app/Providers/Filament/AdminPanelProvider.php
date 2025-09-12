<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    private const PANEL_ID = 'admin';
    private const PANEL_PATH = '/admin';
    private const RESOURCES_PATH = 'App\\Filament\\Admin\\Resources';
    private const PAGES_PATH = 'App\\Filament\\Admin\\Pages';
    private const WIDGETS_PATH = 'App\\Filament\\Admin\\Widgets';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id(self::PANEL_ID)
            ->path(self::PANEL_PATH)
            ->login()
            ->colors($this->getPanelColors())
            ->userMenuItems($this->getUserMenuItems())
            ->spa()
            ->brandName(__('app.dashboard'))
            ->favicon(asset('favicon.ico'))
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: self::RESOURCES_PATH
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: self::PAGES_PATH
            )
            ->pages($this->getDefaultPages())
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: self::WIDGETS_PATH
            )
            ->widgets([])
            ->middleware($this->getMiddleware())
            ->authMiddleware($this->getAuthMiddleware());
    }

    private function getPanelColors(): array
    {
        return [
            'primary' => Color::Amber,
        ];
    }

    private function getDefaultPages(): array
    {
        return [
            Dashboard::class,
        ];
    }

    private function getMiddleware(): array
    {
        return [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            SetLocale::class,
        ];
    }

    private function getAuthMiddleware(): array
    {
        return [
            Authenticate::class,
        ];
    }

    private function getUserMenuItems(): array
    {
        $currentLocale = app()->getLocale();
        $isArabic = $currentLocale === 'ar';

        return [
            'language_switcher' => MenuItem::make()
                ->label($isArabic ? '🇺🇸 English' : '🇸🇦 العربية')
                ->url(request()->fullUrlWithQuery(['lang' => $isArabic ? 'en' : 'ar']))
                ->icon('heroicon-o-language')
                ->sort(1),
            'profile' => MenuItem::make()
                ->label(__('app.profile'))
                ->icon('heroicon-o-user-circle')
                ->sort(2),
        ];
    }
}
