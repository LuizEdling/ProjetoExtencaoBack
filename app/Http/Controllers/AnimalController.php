<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Repositories\AnimalRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** update/destroy ainda não persistem no repositório. */
class AnimalController extends Controller
{
    public function __construct(
        protected AnimalRepository $animals,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Animal::class);

        return response()->json($this->animals->allOrdered());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Animal::class);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'raca' => ['required', 'string', 'max:255'],
            'data_ficha' => ['required', 'date', 'date_format:Y-m-d'],
            'especie' => ['required', 'string', 'max:255'],
            'sexo' => ['required', 'string', Rule::in(['Macho', 'Fêmea'])],
            'idade' => ['required', 'integer', 'min:0', 'max:50'],
            'peso' => ['required', 'numeric', 'min:0.01', 'max:200'],
            'cor' => ['required', 'string', 'max:255'],
            'data_entrada' => ['required', 'date', 'date_format:Y-m-d'],
            'observacoes' => ['required', 'string'],
        ]);

        $animal = $this->animals->create($validated);

        return response()->json([
            'message' => 'Animal cadastrado com sucesso.',
            'data' => $animal,
        ], 201);
    }

    public function update(Request $request, Animal $animal)
    {
        $this->authorize('update', $animal);

        return response()->noContent();
    }

    public function destroy(Animal $animal)
    {
        $this->authorize('delete', $animal);

        return response()->noContent();
    }
}
