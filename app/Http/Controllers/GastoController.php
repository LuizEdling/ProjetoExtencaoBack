<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'valor' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'data' => ['required', 'date', 'date_format:Y-m-d'],
            'descricao' => ['required', 'string', 'max:2000'],
        ]);

        $gasto = Gasto::create($validated);

        return response()->json($gasto, 201);
    }

    public function update(Request $request, Gasto $gasto)
    {
        $validated = $request->validate([
            'valor' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'data' => ['required', 'date', 'date_format:Y-m-d'],
            'descricao' => ['required', 'string', 'max:2000'],
        ]);

        $gasto->update($validated);

        return response()->json($gasto->fresh());
    }

    public function destroy(Gasto $gasto)
    {
        $gasto->delete();

        return response()->noContent();
    }
}
