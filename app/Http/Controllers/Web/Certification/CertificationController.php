<?php
// app/Http/Controllers/Web/Certification/CertificationController.php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Certification;

use App\Http\Controllers\Controller;
use App\Services\Certification\CertificationService;
use Illuminate\View\View;

final class CertificationController extends Controller
{
    public function __construct(
        private readonly CertificationService $service
    ) {}

    public function checkout(): View
    {
        return view('web.certification.checkout');
    }

    public function show(string $code): View
    {
        $certification = $this->service->getByCode($code);

        abort_if(!$certification, 404);

        return view('web.certification.show', compact('certification'));
    }
}
