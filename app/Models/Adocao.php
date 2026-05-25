<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adocao extends Model
{
    /** Tabela singular: o inflector pluraliza "Adocao" como "adocaos", que é inválido. */
    protected $table = 'adocao';

    protected $fillable = [
        'animal_id',
        'adotante_id',
        'data_adocao',
        'doc_adocao',
    ];

    /**
     * @return BelongsTo<Animal, $this>
     */
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * @return BelongsTo<Adotante, $this>
     */
    public function adotante(): BelongsTo
    {
        return $this->belongsTo(Adotante::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_adocao' => 'date',
        ];
    }
}
