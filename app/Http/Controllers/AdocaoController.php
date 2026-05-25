<?php

namespace App\Http\Controllers;

use App\Models\Adocao;
use App\Models\Animal;
use App\Models\AnimalState;
use App\Repositories\AdocaoRepository;
use App\Services\ContratoAdocaoService;
use App\Services\PainelQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdocaoController extends Controller
{
    public function __construct(
        protected AdocaoRepository $adocoes,
        protected ContratoAdocaoService $contratoAdocao,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Adocao::class);

        return response()->json($this->adocoes->allOrdered());
    }

    public function gerarContrato(Adocao $adocao)
    {
        $this->authorize('generateContrato', $adocao);

        return $this->contratoAdocao->streamPdf($adocao);
    }

    /**
     * Rota legada: `{id}` é o ID do registro em `adocao` (não do animal).
     */
    public function gerarContratoLegado(string $id)
    {
        $adocao = Adocao::query()->with(['animal', 'adotante'])->findOrFail($id);
        $this->authorize('generateContrato', $adocao);

        return $this->contratoAdocao->streamPdf($adocao);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Adocao::class);

        $validated = $request->validate([
            'animal_id' => ['required', 'integer', 'exists:animals,id'],
            'adotante_id' => ['required', 'integer', 'exists:adotantes,id'],
            'data' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'data_adocao' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'doc_adocao' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $dataAdocao = $validated['data']
            ?? $validated['data_adocao']
            ?? now()->toDateString();

        $docAdocao = $validated['doc_adocao'] ?? '';

        $adotadoStateId = AnimalState::query()
            ->where('nome', PainelQueryService::STATE_ADOTADO)
            ->value('id');

        if ($adotadoStateId === null) {
            throw ValidationException::withMessages([
                'animal_id' => ['Estado "Adotado" não encontrado no cadastro.'],
            ]);
        }

        try {
            $adocao = DB::transaction(function () use ($validated, $dataAdocao, $docAdocao, $adotadoStateId): Adocao {
                /** @var Animal $animal */
                $animal = Animal::query()
                    ->lockForUpdate()
                    ->with('animalState')
                    ->findOrFail($validated['animal_id']);

                if ((int) $animal->animal_state_id === (int) $adotadoStateId) {
                    throw ValidationException::withMessages([
                        'animal_id' => ['Este animal já está com estado Adotado.'],
                    ]);
                }

                if (Adocao::query()->where('animal_id', $animal->id)->exists()) {
                    throw ValidationException::withMessages([
                        'animal_id' => ['Já existe um registro de adoção para este animal.'],
                    ]);
                }

                $adocao = Adocao::query()->create([
                    'animal_id' => $animal->id,
                    'adotante_id' => $validated['adotante_id'],
                    'data_adocao' => $dataAdocao,
                    'doc_adocao' => $docAdocao,
                ]);

                $animal->update([
                    'animal_state_id' => $adotadoStateId,
                    'animal_state_changed_at' => now(),
                ]);

                return $adocao;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'UNIQUE constraint failed')
                || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw ValidationException::withMessages([
                    'animal_id' => ['Já existe um registro de adoção para este animal.'],
                ]);
            }
            throw $e;
        }

        $adocao->load(['animal.animalState', 'adotante']);

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
            'data' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'data_adocao' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'doc_adocao' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $dataAdocao = $validated['data']
            ?? $validated['data_adocao']
            ?? $adocao->data_adocao?->toDateString();

        $payload = [
            'animal_id' => $validated['animal_id'],
            'adotante_id' => $validated['adotante_id'],
            'data_adocao' => $dataAdocao,
        ];

        if (array_key_exists('doc_adocao', $validated)) {
            $payload['doc_adocao'] = $validated['doc_adocao'] ?? '';
        }

        $this->adocoes->update($adocao, $payload);

        return response()->json($adocao->fresh()->load(['animal.animalState', 'adotante']));
    }

    public function destroy(Adocao $adocao)
    {
        $this->authorize('delete', $adocao);

        $this->adocoes->delete($adocao);

        return response()->noContent();
    }
}
