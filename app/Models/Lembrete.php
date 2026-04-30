<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembrete extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'data',
        'visualizado',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
        'visualizado' => 'boolean',
    ];
}
