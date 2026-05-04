<?php

use App\Http\Controllers\AdocaoController;
use App\Models\Adocao;
use App\Models\Animal;
use App\Models\Adotante;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('adocoes', function () {
    $adocoes = Adocao::with(['animal', 'adotante'])->get();
    return view('adocoes.index', compact('adocoes'));
})->name('adocoes.index');

Route::get('adocoes/create', function () {
    $animais = Animal::all();
    $adotantes = Adotante::all();
    return view('adocoes.create', compact('animais', 'adotantes'));
})->name('adocoes.create');

Route::post('adocoes', function () {
    // Handle store
    $validated = request()->validate([
        'animal_id' => 'required|integer|exists:animals,id',
        'adotante_id' => 'required|integer|exists:adotantes,id',
        'data_adocao' => 'required|date',
        'doc_adocao' => 'required|string|max:255',
    ]);

    Adocao::create($validated);

    return redirect()->route('adocoes.index');
})->name('adocoes.store');

Route::delete('adocoes/{adocao}', function (Adocao $adocao) {
    $adocao->delete();
    return redirect()->route('adocoes.index');
})->name('adocoes.destroy');
