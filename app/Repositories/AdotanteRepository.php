<?php

namespace App\Repositories;

use App\Models\Adotante;
use Illuminate\Database\Eloquent\Collection;

class AdotanteRepository
{
    /**
     * Listagem com filtros
     */
    public function filter(array $filters): Collection
    {
        $query = Adotante::query();

        if (!empty($filters['nome'])) {
            $query->where('nome', 'like', '%' . $filters['nome'] . '%');
        }

        if (!empty($filters['cpf'])) {
            $digits = preg_replace('/\D+/', '', (string) $filters['cpf']);
            if ($digits !== '') {
                $query->where('cpf', 'like', '%' . $digits . '%');
            }
        }

        return $query
            ->orderBy('nome')
            ->get();
    }

    public function create(array $data): Adotante
    {
        return Adotante::create($data);
    }

    public function update(Adotante $adotante, array $data): bool
    {
        return $adotante->update($data);
    }

    public function delete(Adotante $adotante): bool
    {
        return $adotante->delete();
    }
}
