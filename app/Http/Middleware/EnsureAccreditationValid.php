<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CertifiedCenter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAccreditationValid
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if (! $center instanceof CertifiedCenter) {
            return $next($request);
        }

        if ($this->isAuthRoute($request)) {
            return $next($request);
        }

        if (! $center->canPerformActions()) {
            if ($request->isMethod('GET')) {
                session()->flash(
                    'accreditation_warning',
                    $center->accreditationBlockReason()
                );

                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $center->accreditationBlockReason(),
                ], Response::HTTP_FORBIDDEN);
            }

            return back()->with(
                'accreditation_error',
                $center->accreditationBlockReason()
            );
        }

        return $next($request);
    }

    private function isAuthRoute(Request $request): bool
    {
        $path = $request->path();

        return str_contains($path, 'login')
            || str_contains($path, 'logout')
            || str_contains($path, 'password');
    }
}
