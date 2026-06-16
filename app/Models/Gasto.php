<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = [
        'valor',
        'doacao',
        'data',
        'descricao',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'date',
            'valor' => 'decimal:2',
            'doacao' => 'boolean',
        ];
    }
}
