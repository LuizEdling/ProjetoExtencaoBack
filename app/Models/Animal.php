<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use SoftDeletes;

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
        'animal_state_id',
        'animal_state_changed_at',
    ];

    /**
     * @return BelongsTo<AnimalState, $this>
     */
    public function animalState(): BelongsTo
    {
        return $this->belongsTo(AnimalState::class);
    }

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
            'animal_state_changed_at' => 'datetime',
        ];
    }
}
