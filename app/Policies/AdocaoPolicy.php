<?php

namespace App\Policies;

use App\Models\Adocao;
use App\Models\User;

class AdocaoPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Adocao $adocao): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Adocao $adocao): bool
    {
        return true;
    }

    public function delete(?User $user, Adocao $adocao): bool
    {
        return true;
    }

    public function restore(?User $user, Adocao $adocao): bool
    {
        return true;
    }

    public function forceDelete(?User $user, Adocao $adocao): bool
    {
        return true;
    }
}
