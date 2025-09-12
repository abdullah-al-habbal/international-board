<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\AccreditationRequest;

use App\Http\Controllers\Controller;
use App\Services\AccreditationRequest\AccreditationRequestService;
use Illuminate\View\View;

final class AccreditationRequestController extends Controller
{
    public function __construct(private readonly AccreditationRequestService $service) {}

    public function status(int $centerId): View
    {
        $request = $this->service->getLatestForCenter($centerId);
        return view('web.accreditation.status', compact('request'));
    }
}
