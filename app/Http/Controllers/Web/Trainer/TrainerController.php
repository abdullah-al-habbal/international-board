<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Trainer\TrainerIndexRequest;
use App\Services\Trainer\TrainerService;
use Illuminate\View\View;

final class TrainerController extends Controller
{
    public function __construct(private readonly TrainerService $service) {}

    public function index(TrainerIndexRequest $request): View
    {
        $trainers = $this->service->listActive(
            filters: $request->filters(),
            perPage: 12
        );

        return view('web.trainers.index', compact('trainers'));
    }

    public function show(int $trainer): View
    {
        $trainerModel = $this->service->findActive($trainer);

        abort_if($trainerModel === null, 404);

        return view('web.trainers.show', ['trainer' => $trainerModel]);
    }

    public function evaluation(): View
    {
        $evaluationText = $this->service->getEvaluationText();

        return view('web.trainers.evaluation', compact('evaluationText'));
    }
}
