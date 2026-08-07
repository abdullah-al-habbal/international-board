<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Trainer\Pages\TrainerProfilePage;
use App\Filament\Trainer\Widgets\WelcomeWidget;
use App\Http\Middleware\EnsureTrainerIsAccredited;
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

final class TrainerPanelProvider extends PanelProvider
{
    use ResolvesFilamentColor;

    public function panel(Panel $panel): Panel
    {
        $config = config('panels.trainer');

        return $panel
            ->id($config['id'])
            ->path($config['path'])
            ->login()
            ->colors(['primary' => $this->resolveColor($config['color'])])
            ->authGuard($config['guard'])
            ->authPasswordBroker($config['password_broker'])
            ->profile(TrainerProfilePage::class)
            ->userMenuItems($this->getUserMenuItems())
            ->spa()
            ->brandName(__('app.trainer_dashboard'))
            ->favicon(asset('assets/website/images/logo.webp'))
            ->discoverResources(in: app_path('Filament/Trainer/Resources'), for: $config['resources_path'])
            ->discoverPages(in: app_path('Filament/Trainer/Pages'), for: $config['pages_path'])
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Trainer/Widgets'), for: $config['widgets_path'])
            ->widgets([
                WelcomeWidget::class,
            ])
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
            EnsureTrainerIsAccredited::class,
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
        return [
            'profile' => MenuItem::make()
                ->label(__('app.profile'))
                ->icon('heroicon-o-user-circle')
                ->sort(2),
        ];
    }
}
