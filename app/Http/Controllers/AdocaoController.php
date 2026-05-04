<?php

namespace App\Http\Controllers;

use App\Models\Adocao;
use App\Repositories\AdocaoRepository;
use Illuminate\Http\Request;

class AdocaoController extends Controller
{
    public function __construct(
        protected AdocaoRepository $adocoes,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Adocao::class);

        return response()->json($this->adocoes->allOrdered());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Adocao::class);

        $validated = $request->validate([
            'animal_id' => ['required', 'integer', 'exists:animals,id'],
            'adotante_id' => ['required', 'integer', 'exists:adotantes,id'],
            'data_adocao' => ['required', 'date', 'date_format:Y-m-d'],
            'doc_adocao' => ['required', 'string', 'max:255'],
        ]);

        $adocao = $this->adocoes->create($validated);
        $adocao->load(['animal', 'adotante']);

        return response()->json([
            'message' => 'Adoção cadastrada com sucesso.',
            'data' => $adocao,
        ], 201);
    }

    public function update(Request $request, Adocao $adocao)
    {
        $this->authorize('update', $adocao);

        $validated = $request->validate([
            'animal_id' => ['required', 'integer', 'exists:animals,id'],
            'adotante_id' => ['required', 'integer', 'exists:adotantes,id'],
            'data_adocao' => ['required', 'date', 'date_format:Y-m-d'],
            'doc_adocao' => ['required', 'string', 'max:255'],
        ]);

        $this->adocoes->update($adocao, $validated);

        return response()->json($adocao->fresh()->load(['animal', 'adotante']));
    }

    public function destroy(Adocao $adocao)
    {
        $this->authorize('delete', $adocao);

        $this->adocoes->delete($adocao);

        return response()->noContent();
    }
}
