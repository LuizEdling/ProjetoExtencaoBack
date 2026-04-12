<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalCatalogEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnimalCatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Animal::class);

        $validated = $request->validate([
            'kind' => ['required', 'string', Rule::in(['raca', 'especie', 'cor'])],
        ]);

        $names = AnimalCatalogEntry::query()
            ->where('kind', $validated['kind'])
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();

        return response()->json($names);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Animal::class);

        $validated = $request->validate([
            'kind' => ['required', 'string', Rule::in(['raca', 'especie', 'cor'])],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $trimmed = trim($validated['name']);
        if ($trimmed === '') {
            return response()->json(['message' => 'O nome não pode ser vazio.'], 422);
        }

        $existing = AnimalCatalogEntry::query()
            ->where('kind', $validated['kind'])
            ->whereRaw('LOWER(name) = LOWER(?)', [$trimmed])
            ->first();

        if ($existing !== null) {
            return response()->json(['name' => $existing->name], 200);
        }

        $entry = AnimalCatalogEntry::create([
            'kind' => $validated['kind'],
            'name' => $trimmed,
        ]);

        return response()->json(['name' => $entry->name], 201);
    }
}
