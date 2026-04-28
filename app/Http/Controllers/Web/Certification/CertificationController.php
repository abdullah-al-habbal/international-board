<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Certification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Certification\CertificationSearchRequest;
use App\Services\Certification\CertificationService;
use Illuminate\View\View;

final class CertificationController extends Controller
{
    public function __construct(private readonly CertificationService $service) {}

    public function index(): View
    {
        return view('web.certifications.index');
    }

    public function search(CertificationSearchRequest $request): View
    {
        $serial        = $request->validated('serial');
        $certification = $serial ? $this->service->getBySerial($serial) : null;
        $notFound      = $serial !== null && $serial !== '' && $certification === null;

        return view('web.certifications.search', compact('certification', 'serial', 'notFound'));
    }

    public function show(string $serial): View
    {
        $certification = $this->service->getBySerial($serial);

        abort_if($certification === null, 404);

        return view('web.certifications.show', compact('certification'));
    }
}
