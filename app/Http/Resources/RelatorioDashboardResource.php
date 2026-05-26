<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin array{
 *     cadastros_por_mes: list<array{ano_mes: string, total: int}>,
 *     estados_clinica: array{esperando_consulta: int, consultado: int, em_cirurgia: int},
 *     abrigados_adotados_por_mes: list<array{ano_mes: string, abrigados: int, adotados: int}>
 * }
 */
class RelatorioDashboardResource extends JsonResource
{
    /** Resposta plana, compatível com o contrato do front (sem wrapper `data`). */
    public static $wrap = null;

    /**
     * @return array{
     *     cadastros_por_mes: list<array{ano_mes: string, total: int}>,
     *     estados_clinica: array{esperando_consulta: int, consultado: int, em_cirurgia: int},
     *     abrigados_adotados_por_mes: list<array{ano_mes: string, abrigados: int, adotados: int}>
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var array<string, mixed> $r */
        $r = $this->resource;

        return [
            'cadastros_por_mes' => $r['cadastros_por_mes'],
            'estados_clinica' => $r['estados_clinica'],
            'abrigados_adotados_por_mes' => $r['abrigados_adotados_por_mes'],
        ];
    }
}
