<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Gasto::query()->orderByDesc('data')->orderByDesc('id');

        if ($request->filled('data')) {
            $validated = $request->validate([
                'data' => ['required', 'date_format:Y-m-d'],
            ]);
            $query->whereDate('data', $validated['data']);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $this->validateGastoPayload($request);
        $gasto = Gasto::create($validated);

        return response()->json($gasto, 201);
    }

    public function update(Request $request, Gasto $gasto)
    {
        $validated = $this->validateGastoPayload($request);
        $gasto->update($validated);

        return response()->json($gasto->fresh());
    }

    public function destroy(Gasto $gasto)
    {
        $gasto->delete();

        return response()->noContent();
    }

    /**
     * @return array<string, mixed>
     */
    private function validateGastoPayload(Request $request): array
    {
        $validated = $request->validate([
            'doacao' => ['sometimes', 'boolean'],
            'valor' => ['required', 'numeric', 'max:999999.99'],
            'data' => ['required', 'date', 'date_format:Y-m-d'],
            'descricao' => ['required', 'string', 'max:2000'],
        ]);

        $doacao = $request->boolean('doacao');
        $valor = (float) $validated['valor'];

        if ($doacao) {
            if ($valor < 0) {
                throw ValidationException::withMessages([
                    'valor' => ['O valor não pode ser negativo.'],
                ]);
            }
        } elseif ($valor < 0.01) {
            throw ValidationException::withMessages([
                'valor' => ['Informe um valor válido (mínimo R$ 0,01).'],
            ]);
        }

        $validated['doacao'] = $doacao;
        $validated['valor'] = $valor;

        return $validated;
    }
}
