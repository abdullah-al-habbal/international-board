<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use App\Providers\Traits\ResolvesFilamentColor;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    use ResolvesFilamentColor;

    public function panel(Panel $panel): Panel
    {
        $config = config('panels.admin');

        return $panel
            ->default()
            ->id($config['id'])
            ->path($config['path'])
            ->login()
            ->colors(['primary' => $this->resolveColor($config['color'])])
            ->userMenuItems($this->getUserMenuItems())
            ->spa()
            ->brandName(__('app.dashboard'))
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: $config['resources_path'])
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: $config['pages_path'])
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: $config['widgets_path'])
            ->widgets([])
            ->middleware($this->getMiddleware())
            ->authMiddleware($this->getAuthMiddleware());
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
