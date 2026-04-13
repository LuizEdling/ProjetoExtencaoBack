<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalState;
use App\Repositories\AnimalRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'animal_state_id' => ['sometimes', 'integer', 'exists:animal_states,id'],
        ]);

        if (! array_key_exists('animal_state_id', $validated)) {
            $validated['animal_state_id'] = AnimalState::query()
                ->where('nome', 'Esperando adoção')
                ->value('id');
        }

        $validated['animal_state_changed_at'] = now();

        $animal = $this->animals->create($validated);
        $animal->load('animalState');

        return response()->json([
            'message' => 'Animal cadastrado com sucesso.',
            'data' => $animal,
        ], 201);
    }

    public function update(Request $request, Animal $animal)
    {
        $this->authorize('update', $animal);

        $fullFieldKeys = [
            'nome', 'raca', 'data_ficha', 'especie', 'sexo', 'idade', 'peso', 'cor', 'data_entrada', 'observacoes',
        ];
        $isFullUpdate = $request->hasAny($fullFieldKeys);

        if ($isFullUpdate) {
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
                'animal_state_id' => ['required', 'integer', 'exists:animal_states,id'],
            ]);
        } else {
            $validated = $request->validate([
                'animal_state_id' => ['required', 'integer', 'exists:animal_states,id'],
            ]);
        }

        if (array_key_exists('animal_state_id', $validated)
            && (int) $validated['animal_state_id'] !== (int) $animal->animal_state_id) {
            $validated['animal_state_changed_at'] = now();
        }

        $this->animals->update($animal, $validated);

        return response()->json($animal->fresh()->load('animalState'));
    }

    public function destroy(Animal $animal)
    {
        $this->authorize('delete', $animal);

        $this->animals->delete($animal);

        return response()->noContent();
    }
}
