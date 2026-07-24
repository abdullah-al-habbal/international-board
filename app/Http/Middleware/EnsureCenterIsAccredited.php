<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CertifiedCenter;
use App\Services\Accreditation\AccreditationGateService;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureCenterIsAccredited
{
    public function __construct(
        private readonly AccreditationGateService $gateService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var CertifiedCenter|null $center */
        $center = Auth::guard('certified_center')->user();

        if (! $center) {
            return $next($request);
        }

        Log::channel('accreditation')->debug('[Center Middleware] Checking accreditation gate', [
            'center_id' => $center->id,
            'route' => $request->route()?->getName(),
            'can_perform' => $this->gateService->currentCenterCanPerformActions(),
        ]);

        if ($this->gateService->currentCenterCanPerformActions()) {
            return $next($request);
        }

        if ($request->routeIs(
            'filament.center.resources.center-accreditation-requests.*',
            'filament.center.pages.dashboard',
        )) {
            return $next($request);
        }

        $reason = $this->gateService->currentCenterAccreditationBlockReason();

        Log::channel('accreditation')->warning('[Center Middleware] Blocked center', [
            'center_id' => $center->id,
            'reason' => $reason,
            'route' => $request->route()?->getName(),
        ]);

        Notification::make()
            ->warning()
            ->title(__('accreditation.blocked.title'))
            ->body($reason)
            ->persistent()
            ->send();

        return redirect()->route('filament.center.resources.center-accreditation-requests.index');
    }
}
