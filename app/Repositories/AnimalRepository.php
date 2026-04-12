<?php

namespace App\Repositories;

use App\Models\Animal;
use Illuminate\Database\Eloquent\Collection;

class AnimalRepository
{
    /**
     * @return Collection<int, Animal>
     */
    public function allOrdered(): Collection
    {
        return Animal::query()->orderByDesc('created_at')->get();
    }

    public function create(array $attributes): Animal
    {
        return Animal::create($attributes);
    }

    public function update(Animal $animal, array $attributes): bool
    {
        return false;
    }

    public function delete(Animal $animal): bool
    {
        return false;
    }
}
