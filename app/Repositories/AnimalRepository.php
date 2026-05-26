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
    public function paginatedOrdered(int $perPage, ?string $search = null): LengthAwarePaginator
    {
        $query = Animal::query()
            ->with('animalState')
            ->orderByDesc('created_at');

        if ($search !== null && $search !== '') {
            $escaped = addcslashes($search, '%_\\');
            $term = '%'.$escaped.'%';
            $query->where(function ($q) use ($term) {
                $q->where('nome', 'like', $term)
                    ->orWhere('raca', 'like', $term)
                    ->orWhere('especie', 'like', $term)
                    ->orWhere('microchip', 'like', $term);
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
