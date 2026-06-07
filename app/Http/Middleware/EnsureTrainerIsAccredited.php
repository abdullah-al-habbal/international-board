<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Trainer;
use App\Services\Accreditation\AccreditationGateService;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrainerIsAccredited
{
    public function __construct(
        private readonly AccreditationGateService $gateService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Trainer|null $trainer */
        $trainer = Auth::guard('trainer')->user();

        if (!$trainer) {
            return $next($request);
        }

        if ($this->gateService->currentTrainerCanPerformActions()) {
            return $next($request);
        }

        if ($request->routeIs(
            'filament.trainer.resources.trainer-accreditation-requests.*',
            'filament.trainer.pages.dashboard',
        )) {
            return $next($request);
        }

        Notification::make()
            ->warning()
            ->title(__('accreditation.blocked.title'))
            ->body($this->gateService->currentTrainerAccreditationBlockReason())
            ->persistent()
            ->send();

        return redirect()->route('filament.trainer.resources.trainer-accreditation-requests.index');
    }
}
