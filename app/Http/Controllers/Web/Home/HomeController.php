<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Home;

use App\Http\Controllers\Controller;
use App\Services\Web\HomeService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __construct(private readonly HomeService $service) {}

    public function __invoke(): View
    {
        return view('home_page', $this->service->getHomeData());
    }
}
