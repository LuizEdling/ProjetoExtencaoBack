<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $fillable = [
        'nome',
        'raca',
        'data_ficha',
        'especie',
        'sexo',
        'idade',
        'peso',
        'cor',
        'data_entrada',
        'observacoes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_ficha' => 'date',
            'data_entrada' => 'date',
            'idade' => 'integer',
            'peso' => 'float',
        ];
    }
}
