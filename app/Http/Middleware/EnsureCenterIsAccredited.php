<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CertifiedCenter;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCenterIsAccredited
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CertifiedCenter|null $center */
        $center = Auth::guard('certified_center')->user();

        if (! $center) {
            return $next($request);
        }

        if ($request->routeIs('filament.center.resources.accreditation-requests.*')) {
            return $next($request);
        }

        if ($center->canPerformActions()) {
            return $next($request);
        }

        Notification::make()
            ->warning()
            ->title(__('accreditation.blocked.title'))
            ->body($center->accreditationBlockReason())
            ->persistent()
            ->send();

        return redirect()->route('filament.center.resources.accreditation-requests.index');
    }
}
