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

class CenterPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('center')
            ->path('/center')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->authGuard('certified_center')
            ->authPasswordBroker('certified_centers')
            ->userMenuItems($this->getUserMenuItems())
            ->spa()
            ->brandName(__('app.dashboard'))
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Center/Resources'), for: 'App\\Filament\\Center\\Resources')
            ->discoverPages(in: app_path('Filament/Center/Pages'), for: 'App\\Filament\\Center\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Center/Widgets'), for: 'App\\Filament\\Center\\Widgets')
            ->widgets([])
            ->middleware([
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
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
