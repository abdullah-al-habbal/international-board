<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\CertifiedCenter;

use App\Http\Controllers\Controller;
use App\Services\CertifiedCenter\CertifiedCenterService;
use Illuminate\View\View;

final class CertifiedCenterController extends Controller
{
    public function __construct(private readonly CertifiedCenterService $service) {}

    public function index(): View
    {
        $centers = $this->service->getAll();

        return view('web.centers.index', compact('centers'));
    }

    public function show(int $id): View
    {
        $center = $this->service->getById($id);
        abort_if(! $center, 404);

        return view('web.centers.show', compact('center'));
    }
}
