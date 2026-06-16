<?php

namespace App\Repositories;

use App\Models\Animal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AnimalRepository
{
    /**
     * @return Collection<int, Animal>
     */
    public function allOrdered(): Collection
    {
        return Animal::query()
            ->with('animalState')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return LengthAwarePaginator<int, Animal>
     */
    public function paginatedOrdered(
        int $perPage,
        ?string $search = null,
        ?int $animalStateId = null,
        ?string $bairroResgate = null,
        ?string $ruaResgate = null,
    ): LengthAwarePaginator {
        $query = Animal::query()
            ->with('animalState')
            ->orderByDesc('created_at');

        if ($animalStateId !== null) {
            $query->where('animal_state_id', $animalStateId);
        }

        if ($bairroResgate !== null && $bairroResgate !== '') {
            $escaped = addcslashes($bairroResgate, '%_\\');
            $query->where('bairro_resgate', 'like', '%'.$escaped.'%');
        }

        if ($ruaResgate !== null && $ruaResgate !== '') {
            $escaped = addcslashes($ruaResgate, '%_\\');
            $query->where('rua_resgate', 'like', '%'.$escaped.'%');
        }

        if ($search !== null && $search !== '') {
            $escaped = addcslashes($search, '%_\\');
            $term = '%'.$escaped.'%';
            $query->where(function ($q) use ($term) {
                $q->where('nome', 'like', $term)
                    ->orWhere('raca', 'like', $term)
                    ->orWhere('especie', 'like', $term)
                    ->orWhere('microchip', 'like', $term)
                    ->orWhere('numero_protocolo', 'like', $term)
                    ->orWhere('bairro_resgate', 'like', $term)
                    ->orWhere('rua_resgate', 'like', $term);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $attributes): Animal
    {
        return Animal::create($attributes);
    }

    public function update(Animal $animal, array $attributes): bool
    {
        return $animal->update($attributes);
    }

    public function delete(Animal $animal): bool
    {
        return $animal->delete();
    }
}
