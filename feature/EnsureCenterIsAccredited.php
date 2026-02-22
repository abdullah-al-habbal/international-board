<?php

// filePath: app/Http/Middleware/EnsureCenterIsAccredited.php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CertifiedCenter;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied to the Center Filament panel.
 * Allows the AccreditationRequests resource through unconditionally so the
 * center can always see its own request history and submit a new one.
 * All other panel routes are blocked when the center has no active accreditation.
 */
class EnsureCenterIsAccredited
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if (! $center instanceof CertifiedCenter) {
            return $next($request);
        }

        // Always allow the accreditation-requests resource so the center can
        // submit or track its request.
        if ($this->isAccreditationRequestRoute($request)) {
            return $next($request);
        }

        if (! $center->canPerformActions()) {
            $reason = $center->accreditationBlockReason()
                ?? __('accreditation.blocked.generic');

            Notification::make()
                ->title(__('accreditation.blocked.title'))
                ->body($reason)
                ->warning()
                ->send();

            return redirect()->route('filament.center.resources.accreditation-requests.index');
        }

        return $next($request);
    }

    private function isAccreditationRequestRoute(Request $request): bool
    {
        // Matches any URL segment that belongs to accreditation-requests.
        return str_contains($request->path(), 'accreditation-requests');
    }
}
