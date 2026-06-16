<?php

use App\Http\Controllers\AdotanteController;
use App\Http\Controllers\AdocaoController;
use App\Http\Controllers\AnimalCatalogController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalStateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\LembreteController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('catalog', [AnimalCatalogController::class, 'index']);
    Route::post('catalog', [AnimalCatalogController::class, 'store']);

    Route::get('animal-states', [AnimalStateController::class, 'index']);

    Route::get('painel', [PainelController::class, 'index']);

    Route::get('relatorios/dashboard', [RelatorioController::class, 'dashboard']);
    Route::post('relatorios/export', [RelatorioController::class, 'export']);

    Route::get('animals/proximo-protocolo', [AnimalController::class, 'proximoProtocolo']);
    Route::apiResource('animals', AnimalController::class)->except(['show']);
    Route::apiResource('adotantes', AdotanteController::class)->except(['show']);
    Route::post('adocoes/{adocao}/contrato', [AdocaoController::class, 'gerarContrato']);
    Route::get('adocoes', [AdocaoController::class, 'index'])->name('adocoes.index');
    Route::post('adocoes', [AdocaoController::class, 'store'])->name('adocoes.store');
    Route::match(['put', 'patch'], 'adocoes/{adocao}', [AdocaoController::class, 'update'])->name('adocoes.update');
    Route::delete('adocoes/{adocao}', [AdocaoController::class, 'destroy'])->name('adocoes.destroy');
    Route::apiResource('lembretes', LembreteController::class)->except(['show']);
    Route::apiResource('gastos', GastoController::class)->except(['show']);

    // Geração de PDF do contrato; `{id}` = ID da adoção (`adocao.id`)
    Route::get('/contrato/{id}', [AdocaoController::class, 'gerarContratoLegado']);
    //Route::get('/ficha/{id}/pdf', [AnimalController::class, 'generateFichaPdf']);
});

