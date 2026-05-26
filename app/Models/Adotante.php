<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adotante extends Model
{
    protected $fillable = [
        'nome',
        'cpf',
        'telefone',
        'rg',
        'endereco',
        'bairro',
        'cidade',
        'uf',
    ];
}
