<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class HealthCheckController extends Controller
{
    public function __invoke(Request $req)
    {
        $token = $req->header('X-Health-Token');
        $envToken = env('HEALTH_TOKEN');
        if ($envToken && $token !== $envToken) {
            return response()->json(['status' => 'forbidden'], 403);
        }
        try {
            DB::connection()->getPdo();
            $db = true;
        } catch (\Throwable $e) {
            $db = false;
        }
        return response()->json([
            'status' => 'ok',
            'db' => $db,
            'time' => now()->toIso8601String(),
        ], $db ? 200 : 500);
    }
}
