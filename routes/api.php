<?php

use App\Http\Controllers\AdotanteController;
use App\Http\Controllers\AdocaoController;
use App\Http\Controllers\AnimalCatalogController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalStateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LembreteController;
use App\Http\Controllers\PainelController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('catalog', [AnimalCatalogController::class, 'index']);
    Route::post('catalog', [AnimalCatalogController::class, 'store']);

    Route::get('animal-states', [AnimalStateController::class, 'index']);

    Route::get('painel', [PainelController::class, 'index']);

    Route::apiResource('animals', AnimalController::class)->except(['show']);
    Route::apiResource('adotantes', AdotanteController::class)->except(['show']);
    Route::apiResource('adocoes', AdocaoController::class)->except(['show']);
    Route::apiResource('lembretes', LembreteController::class)->except(['show']);
});
