<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CertifiedCenter;
use App\Services\Accreditation\AccreditationGateService;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCenterIsAccredited
{
    public function __construct(
        private readonly AccreditationGateService $gateService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        /** @var CertifiedCenter|null $center */
        $center = Auth::guard('certified_center')->user();

        if (!$center) {
            return $next($request);
        }

        if ($this->gateService->currentCenterCanPerformActions()) {
            return $next($request);
        }

        if ($request->routeIs('filament.center.resources.accreditation-requests.*') ||
            $request->routeIs('filament.center.resources.center-financial-requests.*')) {
            return $next($request);
        }

        Notification::make()
            ->warning()
            ->title(__('accreditation.blocked.title'))
            ->body($this->gateService->currentCenterAccreditationBlockReason())
            ->persistent()
            ->send();

        return redirect()->route('filament.center.resources.accreditation-requests.index');
    }
}
