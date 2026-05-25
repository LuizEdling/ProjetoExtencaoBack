<?php

namespace App\Repositories;

use App\Models\Adocao;
use Illuminate\Database\Eloquent\Collection;

class AdocaoRepository
{
    /**
     * @return Collection<int, Adocao>
     */
    public function allOrdered(): Collection
    {
        return Adocao::query()
            ->with(['animal.animalState', 'adotante'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $attributes): Adocao
    {
        return Adocao::create($attributes);
    }

    public function update(Adocao $adocao, array $attributes): bool
    {
        return $adocao->update($attributes);
    }

    public function delete(Adocao $adocao): bool
    {
        return $adocao->delete();
    }
}
