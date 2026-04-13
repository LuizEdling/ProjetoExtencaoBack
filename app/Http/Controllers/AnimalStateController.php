<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalState;
use Illuminate\Http\JsonResponse;

class AnimalStateController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Animal::class);

        $rows = AnimalState::query()
            ->orderBy('id')
            ->get(['id', 'nome']);

        return response()->json($rows);
    }
}
