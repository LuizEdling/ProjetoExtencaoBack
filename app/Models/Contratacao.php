<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contratacao extends Model
{
    protected $table = 'contratacao';

    protected $fillable = [
        'adocao_id',
        'html_gerado',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'html_gerado',
    ];

    /**
     * @return BelongsTo<Adocao, $this>
     */
    public function adocao(): BelongsTo
    {
        return $this->belongsTo(Adocao::class);
    }
}
