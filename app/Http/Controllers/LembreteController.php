<?php

namespace App\Http\Controllers;

use App\Models\Lembrete;
use Illuminate\Http\Request;

class LembreteController extends Controller
{
    public function index()
    {
        return Lembrete::orderBy('data')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data' => 'required|date',
        ]);

        return Lembrete::create($data);
    }

    public function update(Request $request, $id)
    {
        $lembrete = Lembrete::findOrFail($id);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data' => 'required|date',
            'visualizado' => 'boolean',
        ]);

        $lembrete->update($data);

        return $lembrete;
    }

    public function destroy($id)
    {
        Lembrete::destroy($id);

        return response()->noContent();
    }
}
