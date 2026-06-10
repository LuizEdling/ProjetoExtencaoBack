<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembrete extends Model
{
    public const TIPO_ONCE = 'once';

    public const TIPO_EVERY_N_DAYS = 'every_n_days';

    public const TIPO_WEEKDAY = 'weekday';

    public const TIPO_DAY_OF_MONTH = 'day_of_month';

    /** @var list<string> */
    public const TIPOS_RECORRENCIA = [
        self::TIPO_ONCE,
        self::TIPO_EVERY_N_DAYS,
        self::TIPO_WEEKDAY,
        self::TIPO_DAY_OF_MONTH,
    ];

    protected $fillable = [
        'nome',
        'descricao',
        'data',
        'tipo_recorrencia',
        'intervalo_dias',
        'dia_semana',
        'dia_mes',
        'data_fim',
        'hora',
        'ativo',
        'visualizado',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
        'data_fim' => 'date:Y-m-d',
        'visualizado' => 'boolean',
        'ativo' => 'boolean',
        'intervalo_dias' => 'integer',
        'dia_semana' => 'integer',
        'dia_mes' => 'integer',
    ];
}
