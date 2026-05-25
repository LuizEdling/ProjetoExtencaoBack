<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalState;
use App\Repositories\AnimalRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AnimalController extends Controller
{
    public function __construct(
        protected AnimalRepository $animals,
    ) {}

    /** @param array<string, mixed> $validated */
    private function normalizeMicrochipForCreate(array &$validated): void
    {
        if (! array_key_exists('microchip', $validated)) {
            $validated['microchip'] = null;

            return;
        }

        $v = $validated['microchip'];
        $validated['microchip'] = ($v === null || $v === '') ? null : (string) $v;
    }

    /** @param array<string, mixed> $validated */
    private function normalizeMicrochipForFullUpdate(array &$validated): void
    {
        if (! array_key_exists('microchip', $validated)) {
            return;
        }

        $v = $validated['microchip'];
        $validated['microchip'] = ($v === null || $v === '') ? null : (string) $v;
    }

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
            'microchip' => ['nullable', 'string', 'max:15', 'regex:/^\d*$/'],
            'data_ficha' => ['required', 'date', 'date_format:Y-m-d'],
            'especie' => ['required', 'string', 'max:255'],
            'sexo' => ['required', 'string', Rule::in(['Macho', 'Fêmea'])],
            'idade' => ['required', 'integer', 'min:0', 'max:50'],
            'peso' => ['required', 'numeric', 'min:0.01', 'max:200'],
            'cor' => ['required', 'string', 'max:255'],
            'data_entrada' => ['required', 'date', 'date_format:Y-m-d'],
            'observacoes' => ['required', 'string'],
            'animal_state_id' => ['sometimes', 'integer', 'exists:animal_states,id'],
            'vermifugado' => ['sometimes', 'boolean'],
            'vacinado' => ['sometimes', 'boolean'],
            'castrado' => ['sometimes', 'boolean'],
        ]);

        $this->normalizeMicrochipForCreate($validated);

        $validated['vermifugado'] = (bool) ($validated['vermifugado'] ?? false);
        $validated['vacinado'] = (bool) ($validated['vacinado'] ?? false);
        $validated['castrado'] = (bool) ($validated['castrado'] ?? false);

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
            'nome', 'raca', 'microchip', 'data_ficha', 'especie', 'sexo', 'idade', 'peso', 'cor', 'data_entrada', 'observacoes',
        ];
        $isFullUpdate = $request->hasAny($fullFieldKeys);

        if ($isFullUpdate) {
            $validated = $request->validate([
                'nome' => ['required', 'string', 'max:255'],
                'raca' => ['required', 'string', 'max:255'],
                'microchip' => ['nullable', 'string', 'max:15', 'regex:/^\d*$/'],
                'data_ficha' => ['required', 'date', 'date_format:Y-m-d'],
                'especie' => ['required', 'string', 'max:255'],
                'sexo' => ['required', 'string', Rule::in(['Macho', 'Fêmea'])],
                'idade' => ['required', 'integer', 'min:0', 'max:50'],
                'peso' => ['required', 'numeric', 'min:0.01', 'max:200'],
                'cor' => ['required', 'string', 'max:255'],
                'data_entrada' => ['required', 'date', 'date_format:Y-m-d'],
                'observacoes' => ['required', 'string'],
                'animal_state_id' => ['required', 'integer', 'exists:animal_states,id'],
                'vermifugado' => ['required', 'boolean'],
                'vacinado' => ['required', 'boolean'],
                'castrado' => ['required', 'boolean'],
            ]);

            $this->normalizeMicrochipForFullUpdate($validated);
        } else {
            $validated = $request->validate([
                'animal_state_id' => ['sometimes', 'integer', 'exists:animal_states,id'],
                'vermifugado' => ['sometimes', 'boolean'],
                'vacinado' => ['sometimes', 'boolean'],
                'castrado' => ['sometimes', 'boolean'],
            ]);

            $patchKeys = ['animal_state_id', 'vermifugado', 'vacinado', 'castrado'];
            $hasAnyPatchField = false;
            foreach ($patchKeys as $key) {
                if ($request->has($key)) {
                    $hasAnyPatchField = true;
                    break;
                }
            }
            if (! $hasAnyPatchField) {
                throw ValidationException::withMessages([
                    'message' => ['Informe ao menos um campo para atualizar.'],
                ]);
            }
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
