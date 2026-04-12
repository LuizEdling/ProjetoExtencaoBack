<?php

use App\Http\Controllers\AnimalCatalogController;
use App\Http\Controllers\AnimalController;
use Illuminate\Support\Facades\Route;

Route::get('catalog', [AnimalCatalogController::class, 'index']);
Route::post('catalog', [AnimalCatalogController::class, 'store']);

Route::apiResource('animals', AnimalController::class)->except(['show']);
