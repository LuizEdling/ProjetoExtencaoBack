<?php

namespace App\Http\Controllers;

use App\Models\Adotante;
use App\Repositories\AdotanteRepository;
use Illuminate\Http\Request;

class AdotanteController extends Controller
{
    public function __construct(
        protected AdotanteRepository $adotantes,
    ) {}

    /**
     * LISTAGEM COM FILTRO
     */
    public function index(Request $request)
    {
        $filters = $request->only(['nome', 'cpf']);

        return response()->json(
            $this->adotantes->filter($filters)
        );
    }

    /**
     * CRIAR
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:14', 'unique:adotantes,cpf'],
            'telefone' => ['required', 'string', 'max:20'],
        ]);

        $adotante = $this->adotantes->create($validated);

        return response()->json([
            'message' => 'Adotante cadastrado com sucesso.',
            'data' => $adotante,
        ], 201);
    }

    /**
     * ATUALIZAR
     */
    public function update(Request $request, Adotante $adotante)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:14', 'unique:adotantes,cpf,' . $adotante->id],
            'telefone' => ['required', 'string', 'max:20'],
        ]);

        $this->adotantes->update($adotante, $validated);

        return response()->json([
            'message' => 'Adotante atualizado com sucesso.',
            'data' => $adotante->fresh(),
        ]);
    }

    /**
     * DELETAR
     */
    public function destroy(Adotante $adotante)
    {
        $this->adotantes->delete($adotante);

        return response()->noContent();
    }
}
