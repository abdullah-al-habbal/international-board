<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\CertifiedCenter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CertifiedCenter\CenterIndexRequest;
use App\Services\CertifiedCenter\CertifiedCenterService;
use Illuminate\View\View;

final class CertifiedCenterController extends Controller
{
    public function __construct(private readonly CertifiedCenterService $service) {}

    public function index(CenterIndexRequest $request): View
    {
        $centers = $this->service->listActive(
            filters: $request->filters(),
            perPage: 12
        );

        return view('web.centers.index', compact('centers'));
    }

    public function show(int $id): View
    {
        $center = $this->service->findActive($id);

        abort_if($center === null, 404);

        return view('web.centers.show', compact('center'));
    }
}
