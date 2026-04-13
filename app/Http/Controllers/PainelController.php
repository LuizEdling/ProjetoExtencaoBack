<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Services\PainelQueryService;
use Illuminate\Http\JsonResponse;

class PainelController extends Controller
{
    public function __construct(
        protected PainelQueryService $painelQuery,
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Animal::class);

        return response()->json($this->painelQuery->build());
    }
}
