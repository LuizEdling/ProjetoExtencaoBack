<?php

namespace App\Http\Controllers;

use App\Http\Requests\RelatorioFiltrosRequest;
use App\Http\Resources\RelatorioDashboardResource;
use App\Models\Animal;
use App\Services\RelatorioPdfService;
use App\Services\RelatorioQueryService;
use Illuminate\Http\JsonResponse;

class RelatorioController extends Controller
{
    public function __construct(
        protected RelatorioQueryService $relatorioQuery,
        protected RelatorioPdfService $relatorioPdf,
    ) {}

    public function dashboard(RelatorioFiltrosRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Animal::class);

        $filtros = $request->filtrosNormalizados();
        $data = $this->relatorioQuery->buildDashboard($filtros);

        return (new RelatorioDashboardResource($data))->response();
    }

    public function export(RelatorioFiltrosRequest $request)
    {
        $this->authorize('viewAny', Animal::class);

        $filtros = $request->filtrosNormalizados();
        $data = $this->relatorioQuery->buildDashboard($filtros);

        return $this->relatorioPdf->streamPdf($filtros, $data);
    }
}
