<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalState;
use App\Repositories\AnimalRepository;
use Illuminate\Http\Request;
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

    /**
     * Preenche campos opcionais ausentes para persistência (colunas NOT NULL).
     *
     * @param  array<string, mixed>  $validated
     */
    private function applyOptionalAnimalDefaultsForCreate(array &$validated): void
    {
        $validated['nome'] = isset($validated['nome']) ? (string) $validated['nome'] : '';

        $sexo = $validated['sexo'] ?? null;
        $validated['sexo'] = in_array($sexo, ['Macho', 'Fêmea'], true) ? $sexo : 'Macho';

        $validated['idade'] = isset($validated['idade']) ? (int) $validated['idade'] : 0;
        $validated['idade'] = max(0, min(50, $validated['idade']));

        $validated['peso'] = isset($validated['peso']) ? (float) $validated['peso'] : 0.0;
        $validated['peso'] = max(0.0, min(200.0, $validated['peso']));

        $validated['cor'] = isset($validated['cor']) ? (string) $validated['cor'] : '';
        $validated['observacoes'] = isset($validated['observacoes']) ? (string) $validated['observacoes'] : '';

        $dataFicha = $validated['data_ficha'] ?? null;
        $entrada = $validated['data_entrada'] ?? null;
        if ($entrada === null || $entrada === '') {
            $validated['data_entrada'] = $dataFicha;
        }
    }

    /**
     * Garante valores persistíveis em PATCH completo (evita null em colunas NOT NULL).
     *
     * @param  array<string, mixed>  $validated
     */
    private function applyOptionalAnimalDefaultsForFullUpdate(array &$validated, Animal $animal): void
    {
        if (array_key_exists('nome', $validated) && $validated['nome'] === null) {
            $validated['nome'] = '';
        }

        if (array_key_exists('sexo', $validated)) {
            $s = $validated['sexo'];
            $validated['sexo'] = in_array($s, ['Macho', 'Fêmea'], true) ? $s : 'Macho';
        }

        if (array_key_exists('idade', $validated) && $validated['idade'] === null) {
            $validated['idade'] = 0;
        }

        if (array_key_exists('peso', $validated) && $validated['peso'] === null) {
            $validated['peso'] = 0.0;
        }

        if (array_key_exists('cor', $validated) && $validated['cor'] === null) {
            $validated['cor'] = '';
        }

        if (array_key_exists('observacoes', $validated) && $validated['observacoes'] === null) {
            $validated['observacoes'] = '';
        }

        if (array_key_exists('data_entrada', $validated)) {
            $de = $validated['data_entrada'];
            if ($de === null || $de === '') {
                $df = $validated['data_ficha'] ?? $animal->data_ficha;
                $validated['data_entrada'] = $df instanceof \DateTimeInterface
                    ? $df->format('Y-m-d')
                    : (string) $df;
            }
        }
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Animal::class);

        /** Listagem completa (ex.: selects em outros formulários). */
        if ($request->boolean('all')) {
            return response()->json($this->animals->allOrdered());
        }

        $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'q' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $q = $request->input('q');
        $search = is_string($q) ? $q : null;

        return response()->json(
            $this->animals->paginatedOrdered($perPage, $search),
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', Animal::class);

        $validated = $request->validate([
            'nome' => ['nullable', 'string', 'max:255'],
            'raca' => ['required', 'string', 'max:255'],
            'microchip' => ['nullable', 'string', 'max:15', 'regex:/^\d*$/'],
            'data_ficha' => ['required', 'date', 'date_format:Y-m-d'],
            'especie' => ['required', 'string', 'max:255'],
            'sexo' => ['nullable', 'string', 'max:10'],
            'idade' => ['nullable', 'integer', 'min:0', 'max:50'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'cor' => ['nullable', 'string', 'max:255'],
            'data_entrada' => ['nullable', 'date', 'date_format:Y-m-d'],
            'observacoes' => ['nullable', 'string'],
            'animal_state_id' => ['sometimes', 'integer', 'exists:animal_states,id'],
            'vermifugado' => ['sometimes', 'boolean'],
            'vacinado' => ['sometimes', 'boolean'],
            'castrado' => ['sometimes', 'boolean'],
        ]);

        $this->normalizeMicrochipForCreate($validated);
        $this->applyOptionalAnimalDefaultsForCreate($validated);

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
                'nome' => ['sometimes', 'nullable', 'string', 'max:255'],
                'raca' => ['sometimes', 'required', 'string', 'max:255'],
                'microchip' => ['nullable', 'string', 'max:15', 'regex:/^\d*$/'],
                'data_ficha' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
                'especie' => ['sometimes', 'required', 'string', 'max:255'],
                'sexo' => ['sometimes', 'nullable', 'string', 'max:10'],
                'idade' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:50'],
                'peso' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:200'],
                'cor' => ['sometimes', 'nullable', 'string', 'max:255'],
                'data_entrada' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
                'observacoes' => ['sometimes', 'nullable', 'string'],
                'animal_state_id' => ['sometimes', 'integer', 'exists:animal_states,id'],
                'vermifugado' => ['sometimes', 'boolean'],
                'vacinado' => ['sometimes', 'boolean'],
                'castrado' => ['sometimes', 'boolean'],
            ]);

            $this->normalizeMicrochipForFullUpdate($validated);
            $this->applyOptionalAnimalDefaultsForFullUpdate($validated, $animal);
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
