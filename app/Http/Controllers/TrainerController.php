<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Trainer::query()->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->get('country_id'));
        }

        if ($request->filled('specialization')) {
            $query->whereJsonContains('specializations', $request->get('specialization'));
        }

        $trainers = $query->with('country')->paginate(12);

        return view('trainers.index', compact('trainers'));
    }

    public function show(Trainer $trainer): View
    {
        if (! $trainer->is_active) {
            abort(404);
        }

        $trainer->load('country', 'certifications');

        return view('trainers.show', compact('trainer'));
    }

    public function evaluation(): View
    {
        $evaluationText = \App\Models\ApplicationSetting::get('trainer_evaluation_text', __('app.trainer_evaluation_default_text'));

        return view('trainer-evaluation', ['evaluationText' => $evaluationText]);
    }
}
